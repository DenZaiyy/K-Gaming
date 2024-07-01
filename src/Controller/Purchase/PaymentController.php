<?php

namespace App\Controller\Purchase;

use App\Entity\Facture;
use App\Entity\Game;
use App\Entity\Purchase;
use App\Entity\Stock;
use App\Entity\User;
use App\Service\CartService;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/{_locale<%app.supported_locales%>}/order', name: 'payment_')]
class PaymentController extends AbstractController
{
	//Constructor to get the EntityManagerInterface and the UrlGeneratorInterface
	public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $urlGenerator, private RequestStack $requestStack)
	{
	}

	/**
	 * Function to get the PayPal client by using the PayPal SDK and the PayPal credentials in the .env file
	 * @return PayPalHttpClient
	 */
	public function getPaypalClient(): PayPalHttpClient
	{
        //Récupération des identifiants PayPal dans le .env
		$clientID = $this->getParameter('app.paypalClientID');
		$clientSecret = $this->getParameter('app.paypalSecret');

        //Création de l'environnement de test
		$environment = new SandboxEnvironment($clientID, $clientSecret);
        //Création du client PayPal
		return new PayPalHttpClient($environment);
	}

	/**
	 * Fonction permettant de créer une session Stripe pour le paiement
	 */
	#[Route('/create-session-stripe/{reference}', name: 'stripe_checkout')]
	public function index($reference): RedirectResponse
	{
		$productStripe = []; //initialize the array to store the products
		
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference, 'method' => 'stripe']); //get the purchase by the reference
		
		if (!$purchase) { //if the purchase doesn't exist, redirect to the cart
            $this->addFlash('danger', "La commande avec la référence $reference n'existe pas");
			return $this->redirectToRoute('cart_index');
		}
		
		foreach ($purchase->getRecapDetails()->getValues() as $product) {
			//get the product from the recapDetails
			$productData = $this->em->getRepository(Game::class)->findOneBy(['label' => $product->getGameLabel()]); //get the game by the label
			$price = $productData->getPrice() * 100;
			$amount = str_replace('.', '', $price); //replace the dot by nothing to get the price in cents
			
			//add the product to the array
			$productStripe[] = [
				'price_data' => [
					'currency' => 'eur',
					'unit_amount' => $amount,
					'product_data' => [
						'name' => $product->getGameLabel(),
					],
				],
				'quantity' => $product->getQuantity(),
			];
		}
		
		Stripe::setApiKey($this->getParameter('app.stripe_private_key')); //set the Stripe API key from the .env file
		
		//create the session with the Stripe API
		$checkout_session = Session::create([
			'customer_email' => $this->getUser()->getEmail(), //get the user email
			'payment_method_types' => ['card'],
			'line_items' => [
				[$productStripe] //add the products to the session
			],
			'locale' => 'fr',
			'mode' => 'payment',
			'success_url' => $this->urlGenerator->generate(
				'payment_success',
				['reference' => $purchase->getReference()],
				UrlGeneratorInterface::ABSOLUTE_URL
			),
			'cancel_url' => $this->urlGenerator->generate(
				'payment_error',
				['reference' => $purchase->getReference()],
				UrlGeneratorInterface::ABSOLUTE_URL
			),
			'automatic_tax' => [
				'enabled' => true,
			],
		]);
		
		$stripeID = $this->requestStack->getSession()->get('stripeID', []); //get the stripeID from the session
		$stripeID[] = $checkout_session->id; //add the stripeID to the array
		$this->requestStack->getSession()->set('stripeID', $stripeID); //set the stripeID session
		
		
		if ($checkout_session->payment_status === 'paid') {
			//if the payment is paid, set the stripe session ID to the purchase
			$purchase->setStripeSessionId($checkout_session->id);
			$this->em->flush();
		}
		
		return new RedirectResponse($checkout_session->url, 303); //redirect to the Stripe page
	}
	
	//Function to redirect to the success page
	#[Route('/success/{reference}', name: 'success')]
	public function purchaseSuccess($reference, CartService $cartService, MailerInterface $mailer, PdfService $pdf): Response
	{
        //Récupération de la commande par la référence
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);
        //Récupération de l'utilisateur par la commande
		$user = $this->em->getRepository(User::class)->find($purchase->getUser());
        //Récupération du détail de la commande (liste des jeux et quantité)
		$recapDetails = $purchase->getRecapDetails();

        //Vérification si la commande a déjà été payée
		if($purchase->isIsPaid()) {
            //Si oui, redirection vers la page d'accueil avec un message d'erreur
			$this->addFlash('danger', 'La commande a déjà été payée et livrée!');
			return $this->redirectToRoute('app_home');
		}

        //Initialisation du tableau des clés de licence
		$license = [];
		
		//Pour chaque détail de la commande, récupération des clés de licence disponibles
		foreach ($recapDetails as $recapDetail) {
            //Récupération des clés de licence disponibles par jeu et par plateforme et par quantité commandée (méthode personnalisée dans le repository)
			$gameStockID = $this->em->getRepository(Stock::class)->findLicenseKeyAvailableByGamesAndPlatform(
                $recapDetail->getGameId(),
                $recapDetail->getPlatformId(),
                $recapDetail->getQuantity()
            );

            //Pour chaque clé de licence, ajout dans le tableau des clés de licence et mise à jour de la disponibilité de la clé
			foreach ($gameStockID as $gameStock) {
                //Ajout de la clé de licence dans le tableau
				$license[] = $gameStock->getLicenseKey();

                //Mise à jour du stock en attribuant la commande et en mettant la clé de licence comme indisponible
				$gameStock->setPurchase($purchase);
				$gameStock->setIsAvailable(false);

                //Hydratation de l'objet en base de données (persist)
				$this->em->persist($gameStock);
			}
		}
		
		//Vérification si la commande a été payée par Stripe ou PayPal
		if ($purchase->getMethod() === 'stripe') {
            //Si la commande a été payée par Stripe, récupération de l'ID de la session Stripe
			$stripeID = $this->requestStack->getSession()->get('stripeID');
            //Récupération de l'ID de la session Stripe
			$stripeID = $stripeID[0];

            //Mise à jour de la commande avec l'ID de la session Stripe
			$purchase->setStripeSessionId($stripeID);
            //Suppression de la session Stripe
			unset($stripeID);
		} else {
            //Si la commande a été payée par PayPal, récupération de l'ID de la session PayPal
			$paypalID = $this->requestStack->getSession()->get('paypalID');
            //Récupération de l'ID de la session PayPal
			$paypalID = $paypalID[0];

            //Mise à jour de la commande avec l'ID de la session PayPal
			$purchase->setPaypalOrderId($paypalID);
            //Suppression de la session PayPal
			unset($paypalID);
		}

        //Mise à jour de la commande avec le statut payé
		$purchase->setIsPaid(true);

        // Persiste la commande en base de données
		$this->em->persist($purchase);
        //Enregistrement de la commande en base de données
		$this->em->flush();

        //Initialisation du tableau des produits
		$products = [];

        //Boucle sur le contenu du panier pour récupérer les produits commandés
		foreach ($cartService->getTotal() as $item) {
			//Pour chaque produit, récupération du jeu, de la plateforme et de la quantité
			$product = [
				'game' => $item['game'],
				'platform' => $item['platform'],
				'quantity' => $item['quantity'],
			];

            //Ajout du produit dans le tableau des produits
			$products[] = $product;
		}

        //Vidage du panier
		$cartService->removeCartAll(); //remove the cart

        //Initialisation de l'ID unique
        $idUniq = uniqid();

        //Récupération du template de la facture et envoi des données à la vue
        $html = $this->render('order/invoice/facture.html.twig', [
            'user' => $user,
            'games' => $products,
            'purchase' => $purchase,
            'reference' => $purchase->getReference(),
            'facture' => $idUniq,
        ]);

        //Création d'un nouvel objet facture
        $facture = new Facture();

        //Initialisation du chemin d'enregistrement de la facture
        $path = $this->getParameter('pdf_directory');

		//Vérification si le dossier existe, sinon création du dossier
		if(!is_dir($path)) {
			mkdir($path, 0777, true);
		}

        //Initialisation du nom de la facture
        $name = 'facture-' . $idUniq . '.pdf';

        //Enregistrement de la facture en PDF dans le dossier public/uploads/factures du projet
        $pdf->savePdfFile($html, $path . '/' . $name);


        //Association de la facture à la commande
        $facture->setPurchase($purchase);
        //Création de la référence de la facture grâce à l'ID unique
        $facture->setReference($idUniq);
        //Création du nom de la facture
        $facture->setFacture($name);

        //Persiste la facture en base de données
        $this->em->persist($facture);
        //Enregistrement de la facture en base de données
        $this->em->flush();
		
		/* Création du template d'email et envoi des données à la vue pour l'envoi de l'email de confirmation
		 de commande avec la facture en pièce jointe au format PDF */
		$email = (new TemplatedEmail())
            //Ajout de l'expéditeur
			->from(new Address('support@k-grischko.fr', 'K-GAMING - Confirmation de commande'))
            //Ajout du destinataire
			->to($user->getEmail())
            //Ajout du sujet
			->subject('Commande n°' . $purchase->getReference())
            //Ajout du template Twig
			->htmlTemplate('order/invoice/index.html.twig')
            //Ajout des données à la vue Twig
			->context([
				'user' => $user,
				'games' => $products,
				'purchase' => $purchase,
				'license' => $license,
				'reference' => $purchase->getReference(),
			])
            //Ajout de la facture en pièce jointe au format PDF
            ->addPart(
                new DataPart(
                    new File($path . '/' . 'facture-' . $idUniq . '.pdf'),
                    'facture-' . $idUniq . '.pdf',
                    'application/pdf'
                )
            )
		;

        //Envoi de l'email de confirmation de commande
		$mailer->send($email);

        //Redirection vers la page de confirmation de commande avec la référence de la commande et les produits commandés
		return $this->render('order/payment/success.html.twig', [
			'reference' => $reference,
			'products' => $products,
			'purchase' => $purchase,
            'description' => null
		]);
	}
	
	//Function to redirect to the error page
	#[Route('/error/{reference}', name: 'error')]
	public function purchaseError($reference, CartService $cartService): Response
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]); //get the purchase by the reference
		
		if(!$purchase) {
			//if the purchase doesn't exist, redirect to the cart
			return $this->redirectToRoute('cart_index');
		}
		
		$this->em->getRepository(Purchase::class)->remove($purchase, true); //remove the purchase
		
		return $this->render('order/payment/error.html.twig', [
			'reference' => $reference,
            'description' => null
		]);
	}
	
	//Function to create a session with Paypal and redirect to the Paypal page to pay
	#[Route('/create-session-paypal/{reference}', name: 'paypal_checkout')]
	public function createSessionPaypal($reference): RedirectResponse
	{
        //Variable pour vérifier si la commande existe bien en base de données et que la méthode est paypal
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference, 'method' => 'paypal']);
        //Si la commande n'existe pas ou que la méthode n'est pas paypal, on redirige vers le panier
		if (!$purchase) {
            $this->addFlash('danger', "La commande avec la reference $reference n'existe pas");
            //if the purchase doesn't exist, redirect to the cart
			return $this->redirectToRoute('cart_index');
		}

        //Initialisation de la variable pour stocker les produits
		$items = [];
        //Initialisation de la variable pour stocker le total des produits
		$itemTotal = 0;

        //On boucle sur les produits de la commande pour les ajouter à la variable $items et on calcule le total des produits
		foreach ($purchase->getRecapDetails()->getValues() as $product) {

            //On ajoute les produits à la variable $items
			$items[] = [
                //On récupère le nom du jeu
				'name' => $product->getGameLabel(),
                //On récupère la quantité
				'quantity' => $product->getQuantity(),
                //unit_amount est le prix unitaire du produit (prix du jeu) et currency_code est la devise (EUR)
				'unit_amount' => [
                    //On récupère le prix du jeu
					'value' => $product->getPrice(),
                    //On récupère la devise
					'currency_code' => 'EUR',
				]
			];

            //On calcule le total des produits
			$itemTotal += $product->getPrice() * $product->getQuantity();
		}

        //On calcule le prix de la taxe (20%)
		$shipping = round($itemTotal / 100 * 20, 2);
        //On calcule le prix total
		$total = round($itemTotal + $shipping, 2);
        // On arrondi le prix total
		$itemTotal = round($itemTotal, 2);

        //On instancie un objet Paypal pour créer la session de paiement Paypal
		$request = new OrdersCreateRequest();
        //On défini le mode de retour de la requête
		$request->prefer('return=representation');

        //On défini le contenu de la requête
		$request->body = [
            //On défini l'intent de la requête (CAPTURE)
			'intent' => 'CAPTURE',
            //On défini les détails de la requête
			'purchase_units' => [
				[
                    //On défini le montant de la requête
					'amount' => [
                        //On défini la devise
						'currency_code' => 'EUR',
                        //On défini le prix total
						'value' => $total,
                        //On défini le détail de la requête
						'breakdown' => [
                            //On défini le prix total des produits
							'item_total' => [
                                //On défini la devise
								'currency_code' => 'EUR',
                                //On défini le prix total des produits
								'value' => $itemTotal,
							],
                            //On défini le prix de la taxe
							'shipping' => [
                                //On défini la devise
								'currency_code' => 'EUR',
                                //On défini le prix de la taxe
								'value' => $shipping,
							],
						],
					],
                    //On défini les produits de la requête récupérés dans la variable $items
					'items' => $items,
				]
			],
            //On défini le contexte de la requête (URL de redirection en cas de succès ou d'erreur)
			'application_context' => [
                //On défini l'URL de redirection en cas d'erreur
				'cancel_url' => $this->urlGenerator->generate(
                    //On défini la route de redirection en cas d'erreur
					'payment_error',
                    //On défini les paramètres de la route de redirection en cas d'erreur
					['reference' => $purchase->getReference()],
                    //On défini le type de redirection (URL absolue)
					UrlGeneratorInterface::ABSOLUTE_URL
				),
                //On défini l'URL de redirection en cas de succès
				'return_url' => $this->urlGenerator->generate(
                    //On défini la route de redirection en cas de succès
					'payment_success',
                    //On défini les paramètres de la route de redirection en cas de succès
					['reference' => $purchase->getReference()],
                    //On défini le type de redirection (URL absolue)
					UrlGeneratorInterface::ABSOLUTE_URL
				),
			],
		];

        //On récupère le client Paypal
		$client = $this->getPaypalClient();
        //On exécute la requête
		$response = $client->execute($request);

        //Si la requête n'est pas un succès, on redirige vers le panier
		if ($response->statusCode != 201) {
			return $this->redirectToRoute('cart_index');
		}

        //On récupère le lien de redirection vers Paypal
		$approvalLink = '';
        //On boucle sur les liens de la requête
		foreach ($response->result->links as $link) {
            //Si le lien est un lien d'approbation, on le stocke dans la variable $approvalLink
			if ($link->rel == 'approve') {
                //On stocke le lien d'approbation dans la variable $approvalLink
				$approvalLink = $link->href;
			}
		}

        //Si le lien d'approbation est vide, on redirige vers le panier
		if (empty($approvalLink)) {
			return $this->redirectToRoute('cart_index');
		}

        //On stocke l'ID de la session Paypal dans la session
		$paypalID = $this->requestStack->getSession()->get('paypalID', []);
        //On ajoute l'ID de la session Paypal dans la variable $paypalID
		$paypalID[] = $response->result->id;
        //On stocke la variable $paypalID dans la session
		$this->requestStack->getSession()->set('paypalID', $paypalID);

        //On stocke l'ID de la commande dans la session
		$this->em->flush();

        //On redirige vers le lien d'approbation Paypal
		return new RedirectResponse($approvalLink);
	}
}
