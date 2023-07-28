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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
		$clientID = $this->getParameter('app.paypalClientID');
		$clientSecret = $this->getParameter('app.paypalSecret');
		
		$environment = new SandboxEnvironment($clientID, $clientSecret);
		return new PayPalHttpClient($environment);
	}

	/**
	 * Fonction permettant de créer une session Stripe pour le paiement
	 */
	#[Route('/order/create-session-stripe/{reference}', name: 'app_stripe_checkout')]
	public function index($reference): RedirectResponse
	{
		$productStripe = []; //initialize the array to store the products
		
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference, 'method' => 'stripe']); //get the purchase by the reference
		
		if (!$purchase) { //if the purchase doesn't exist, redirect to the cart
            $this->addFlash('danger', "La commande avec la référence $reference n'existe pas");
			return $this->redirectToRoute('app_cart_index');
		}
		
		foreach ($purchase->getRecapDetails()->getValues() as $product) {
			//get the product from the recapDetails
			$productData = $this->em->getRepository(Game::class)->findOneBy(['label' => $product->getGameLabel()]); //get the game by the label
			$amount = str_replace('.', '', $productData->getPrice()); //replace the dot by nothing to get the price in cents
			
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
	#[Route('/order/success/{reference}', name: 'payment_success')]
	public function purchaseSuccess($reference, CartService $cartService, MailerInterface $mailer, PdfService $pdf): Response
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);
		$user = $this->em->getRepository(User::class)->find($purchase->getUser()); //get the user by the purchase
		$recapDetails = $purchase->getRecapDetails(); //get the recapDetails from the purchase

		if($purchase->isIsPaid()) {
			$this->addFlash('danger', 'La commande a déjà été payée et livrée!');
			return $this->redirectToRoute('app_home');
		}

		$license = [];
		
		//search the license key available for the game and the platform and set it to unavailable and set the purchaseID
		foreach ($recapDetails as $recapDetail) {
			$gameStockID = $this->em->getRepository(Stock::class)->findLicenseKeyAvailableByGamesAndPlatform($recapDetail->getGameId(), $recapDetail->getPlatformId(), $recapDetail->getQuantity());

			foreach ($gameStockID as $gameStock) {
				$license[] = $gameStock->getLicenseKey();

				$gameStock->setPurchase($purchase);
				$gameStock->setIsAvailable(false);

				$this->em->persist($gameStock);
			}
		}
		
		//If the purchase is paid by Stripe, set the stripe session ID to purchase
		if ($purchase->getMethod() === 'stripe') {
			$stripeID = $this->requestStack->getSession()->get('stripeID');
			$stripeID = $stripeID[0];
			
			$purchase->setStripeSessionId($stripeID);
			unset($stripeID); //unset the stripeID session
		} else {
			//If the purchase is paid by PayPal, set the PayPal order ID to purchase
			$paypalID = $this->requestStack->getSession()->get('paypalID');
			$paypalID = $paypalID[0];
			$purchase->setPaypalOrderId($paypalID);
			unset($paypalID); //unset the paypalID session
		}
		
		$purchase->setIsPaid(true); //set the purchase to paid
		
		$this->em->persist($purchase); //persist the purchase
		$this->em->flush(); //flush the purchase
		
		$products = []; //initialize the array to store the products
		
		foreach ($cartService->getTotal() as $item) {
			//get the products from the cart
			$product = [
				'game' => $item['game'],
				'platform' => $item['platform'],
				'quantity' => $item['quantity'],
			];
			
			$products[] = $product; //add the product to the array
		}
		
		$cartService->removeCartAll(); //remove the cart

        $idUniq = uniqid();

        $html = $this->render('order/invoice/facture.html.twig', [
            'user' => $user,
            'games' => $products,
            'purchase' => $purchase,
            'reference' => $purchase->getReference(),
            'facture' => $idUniq,
        ]);

        $facture = new Facture();

        $path = $this->getParameter('pdf_directory');
        $name = 'facture-' . $idUniq . '.pdf';

        $pdf->savePdfFile($html, $path . '/' . $name);


        $facture->setPurchase($purchase);
        $facture->setReference($idUniq);
        $facture->setFacture($name);

        $this->em->persist($facture);
        $this->em->flush();
		
		//send the email to the user
		$email = (new TemplatedEmail())
			->from(new Address('support@k-grischko.fr', 'K-GAMING - Confirmation de commande')) //set the sender
			->to($user->getEmail()) //get the user email
			->subject('Commande n°' . $purchase->getReference()) //set the subject
			->htmlTemplate('order/invoice/index.html.twig') //set the template
			->context([
				'user' => $user,
				'games' => $products,
				'purchase' => $purchase,
				'license' => $license,
				'reference' => $purchase->getReference(),
			])
            ->addPart(new DataPart(new File($path . '/' . 'facture-' . $idUniq . '.pdf'), 'facture-' . $idUniq . '.pdf', 'application/pdf'))
		;
		
		$mailer->send($email); //send the email
		
		return $this->render('order/payment/success.html.twig', [
			'reference' => $reference,
			'products' => $products,
			'purchase' => $purchase,
            'description' => null
		]);
	}
	
	//Function to redirect to the error page
	#[Route('/order/error/{reference}', name: 'payment_error')]
	public function purchaseError($reference, CartService $cartService): Response
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]); //get the purchase by the reference
		
		if(!$purchase) {
			//if the purchase doesn't exist, redirect to the cart
			return $this->redirectToRoute('app_cart_index');
		}
		
		$this->em->getRepository(Purchase::class)->remove($purchase, true); //remove the purchase
		
		return $this->render('order/payment/error.html.twig', [
			'reference' => $reference,
            'description' => null
		]);
	}
	
	//Function to create a session with Paypal and redirect to the Paypal page to pay
	#[Route('/order/create-session-paypal/{reference}', name: 'app_paypal_checkout')]
	public function createSessionPaypal($reference): RedirectResponse
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference, 'method' => 'paypal']);
		if (!$purchase) {
            $this->addFlash('danger', "La commande avec la reference $reference n'existe pas");
            //if the purchase doesn't exist, redirect to the cart
			return $this->redirectToRoute('app_cart_index');
		}
		
		$items = [];
		$itemTotal = 0;
		
		foreach ($purchase->getRecapDetails()->getValues() as $product) {
			
			$items[] = [
				'name' => $product->getGameLabel(),
				'quantity' => $product->getQuantity(),
				'unit_amount' => [
					'value' => $product->getPrice(),
					'currency_code' => 'EUR',
				]
			];
			
			$itemTotal += $product->getPrice() * $product->getQuantity();
		}
		
		$shipping = round($itemTotal / 100 * 20, 2);
		$total = round($itemTotal + $shipping, 2);
		$itemTotal = round($itemTotal, 2);
		
		$request = new OrdersCreateRequest();
		$request->prefer('return=representation');
		
		$request->body = [
			'intent' => 'CAPTURE',
			'purchase_units' => [
				[
					'amount' => [
						'currency_code' => 'EUR',
						'value' => $total,
						'breakdown' => [
							'item_total' => [
								'currency_code' => 'EUR',
								'value' => $itemTotal,
							],
							'shipping' => [
								'currency_code' => 'EUR',
								'value' => $shipping,
							],
						],
					],
					'items' => $items,
				]
			],
			'application_context' => [
				'cancel_url' => $this->urlGenerator->generate(
					'payment_error',
					['reference' => $purchase->getReference()],
					UrlGeneratorInterface::ABSOLUTE_URL
				),
				'return_url' => $this->urlGenerator->generate(
					'payment_success',
					['reference' => $purchase->getReference()],
					UrlGeneratorInterface::ABSOLUTE_URL
				),
			],
		];
		
		$client = $this->getPaypalClient();
		$response = $client->execute($request);
		
		if ($response->statusCode != 201) {
			return $this->redirectToRoute('app_cart_index');
		}
		
		$approvalLink = '';
		foreach ($response->result->links as $link) {
			if ($link->rel == 'approve') {
				$approvalLink = $link->href;
			}
		}
		
		if (empty($approvalLink)) {
			return $this->redirectToRoute('app_cart_index');
		}
		
		$paypalID = $this->requestStack->getSession()->get('paypalID', []);
		$paypalID[] = $response->result->id;
		$this->requestStack->getSession()->set('paypalID', $paypalID);
		
		$this->em->flush();
		
		return new RedirectResponse($approvalLink);
	}
}
