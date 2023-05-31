<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Purchase;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaymentController extends AbstractController
{
	//Constructor to get the EntityManagerInterface and the UrlGeneratorInterface
	public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $urlGenerator, private RequestStack $requestStack)
	{
	}
	
	//Function to get the PayPal client by using the PayPal SDK and the PayPal credentials in the .env file
	
	/**
	 * @return PayPalHttpClient
	 */
	public function getPaypalClient(): PayPalHttpClient
	{
		$clientID = $this->getParameter('app.paypalClientID');
		$clientSecret = $this->getParameter('app.paypalSecret');
		
		$environment = new SandboxEnvironment($clientID, $clientSecret);
		return new PayPalHttpClient($environment);
	}
	
	//Function to create a session with Stripe and redirect to the Stripe page to pay
	#[Route('/order/create-session-stripe/{reference}', name: 'app_stripe_checkout')]
	public function index($reference): RedirectResponse
	{
		$productStripe = [];
		
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);
		
		if (!$purchase) {
			return $this->redirectToRoute('app_cart_index');
		}
		
		foreach ($purchase->getRecapDetails()->getValues() as $product) {
			$productData = $this->em->getRepository(Game::class)->findOneBy(['label' => $product->getGameLabel()]);
			$amount = str_replace('.', '', $productData->getPrice());
			
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
		
		Stripe::setApiKey($this->getParameter('app.stripe_private_key'));
		
		$checkout_session = Session::create([
			'customer_email' => $this->getUser()->getEmail(),
			'payment_method_types' => ['card'],
			'line_items' => [[
				$productStripe
			]],
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
//		dd($checkout_session);
		
		$stripeID = $this->requestStack->getSession()->get('stripeID', []);
		$stripeID[] = $checkout_session->id;
		$this->requestStack->getSession()->set('stripeID', $stripeID);
		
		
		if ($checkout_session->payment_status === 'paid') {
			$purchase->setStripeSessionId($checkout_session->id);
			$this->em->flush();
		}
		
		return new RedirectResponse($checkout_session->url, 303);
	}
	
	//Function to redirect to the success page
	#[Route('/order/success/{reference}', name: 'payment_success')]
	public function purchaseSuccess($reference, CartService $cartService): Response
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);
		
		if ($purchase->getMethod() === 'stripe') {
			$stripeID = $this->requestStack->getSession()->get('stripeID');
			$stripeID = $stripeID[0];
			
			$purchase->setStripeSessionId($stripeID);
			unset($stripeID);
		} else {
			$paypalID = $this->requestStack->getSession()->get('paypalID');
			$paypalID = $paypalID[0];
			$purchase->setPaypalOrderId($paypalID);
			unset($paypalID);
		}
		
		$purchase->setIsPaid(true);
		
		$this->em->persist($purchase);
		$this->em->flush();
		
		$products = [];
		
		foreach ($cartService->getTotal() as $item) {
			$product = [
				'game' => $item['game'],
				'platform' => $item['platform'],
				'quantity' => $item['quantity'],
			];
			
			$products[] = $product;
		}
		
		return $this->render('order/success.html.twig', [
			'reference' => $reference,
			'products' => $products,
		]);
	}
	
	//Function to redirect to the error page
	#[Route('/order/error/{reference}', name: 'payment_error')]
	public function purchaseError($reference, CartService $cartService): Response
	{
		return $this->render('order/error.html.twig');
	}
	
	//Function to create a session with Paypal and redirect to the Paypal page to pay
	#[Route('/order/create-session-paypal/{reference}', name: 'app_paypal_checkout')]
	public function createSessionPaypal($reference): RedirectResponse
	{
		$purchase = $this->em->getRepository(Purchase::class)->findOneBy(['reference' => $reference]);
		if (!$purchase) {
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
