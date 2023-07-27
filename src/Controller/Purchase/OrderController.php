<?php

namespace App\Controller\Purchase;

use App\Entity\Address;
use App\Entity\Purchase;
use App\Entity\RecapDetails;
use App\Form\OrderType;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
	public function __construct(private EntityManagerInterface $em, private RequestStack $requestStack)
	{
	}

	/**
	 * Fonction permettant de créer une commande en sélectionnant une adresse de facturation et le moyen de paiement
	 */
	#[Route('/order/create', name: 'order_create')]
	public function index(CartService $cartService, Request $request): Response
	{
		$user = $this->getUser();

		$address = $this->em->getRepository(Address::class)->findBy(['user' => $user]);

		if (!$user) {
			$this->addFlash('danger', 'Vous devez être connecté pour passer une commande.');
			return $this->redirectToRoute('app_login');
		}

		$form = $this->createForm(OrderType::class, null, [
			'user' => $user
		]);

		$url = $request->headers->get('referer');
		$this->requestStack->getSession()->set('previousUrl', $url);

		return $this->render('order/index.html.twig', [
			'form' => $form->createView(),
			'recapCart' => $cartService->getTotal(),
			'cartTotal' => $cartService->getTotalCart(),
			'address' => $address,
		]);
	}

	/**
	 * Fonction permettant de vérifier la commande avant de la valider et de passer au paiement
	 * TODO: Trouver comment supprimer la commande si on quitte la page
	 */
	#[Route('/order/verify', name: 'order_prepare', methods: ['POST'])]
	public function prepareOrder(CartService $cartService, Request $request): Response
	{
		if (!$this->getUser()) {
			return $this->redirectToRoute('app_login');
		}

		$form = $this->createForm(OrderType::class, null, [
			'user' => $this->getUser()
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$delivery = $form->get('addresses')->getData();

			$deliveryForPurchase = $delivery->getAddress() . '</br>';
			$deliveryForPurchase .= $delivery->getCp() . ' - ' . $delivery->getCity();

			$purchase = new Purchase();
			$purchase->setUser($this->getUser());

			$reference = $purchase->getCreatedAt()->format('dmY') . '-' . uniqid();
			$purchase->setReference($reference);

			$purchase->setAddress($delivery);
			$purchase->setDelivery($deliveryForPurchase);
			$purchase->setUserFullName($delivery->getFirstname() . ' ' . $delivery->getLastname());
			$purchase->setIsPaid(0);

			$paymentMethod = $form->get('payment')->getData();
			$purchase->setMethod($paymentMethod);

			$this->em->persist($purchase);

			foreach ($cartService->getTotal() as $product) {
				$recapDetails = new RecapDetails();

				$recapDetails->setOrderProduct($purchase);
				$recapDetails->setQuantity($product['quantity']);
				$recapDetails->setPrice($product['game']->getPrice());
				$recapDetails->setGameLabel($product['game']->getLabel());
				$recapDetails->setGameSlug($product['game']->getSlug());
				$recapDetails->setGameId($product['game']->getId());
				$recapDetails->setPlatformLabel($product['platform']->getLabel());
				$recapDetails->setPlatformSlug($product['platform']->getSlug());
				$recapDetails->setPlatformId($product['platform']->getId());
				$recapDetails->setTotalRecap(
					$product['game']->getPrice() * $product['quantity']
				);

				$this->em->persist($recapDetails);
			}

			$this->em->flush();

			return $this->render('order/recap.html.twig', [
				'method' => $purchase->getMethod(),
				'recapCart' => $cartService->getTotal(),
				'cartTotal' => $cartService->getTotalCart(),
				'delivery' => $deliveryForPurchase,
				'reference' => $purchase->getReference(),
			]);
		}

		return $this->redirectToRoute('app_cart_index');
	}

    #[Route('/order/cancel/{reference}', name: 'order_cancel')]
    public function cancelOrder($reference): Response
    {
        $purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);

        if (!$purchase || $purchase->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($purchase->isIsPaid() == 1) {
            return $this->redirectToRoute('app_home');
        }

        $this->em->remove($purchase);
        $this->em->flush();

        $this->addFlash('success', 'Votre commande a bien été annulée.');

        return $this->redirectToRoute('app_home');
    }
}
