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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
	//Constructor to get the EntityManagerInterface and the UrlGeneratorInterface
	public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $urlGenerator)
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
			$productData = $this->em->getRepository(Game::class)->findOneBy(['label' => $product->getGame()]);
			$amount = str_replace('.', '', $productData->getPrice());
			
			$productStripe[] = [
				'price_data' => [
					'currency' => 'eur',
					'unit_amount' => $amount,
					'product_data' => [
						'name' => $product->getGame(),
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
		
		$purchase->setStripeSessionId($checkout_session->id);
		$this->em->flush();
		
		return new RedirectResponse($checkout_session->url, 303);
	}
	
	//Function to redirect to the success page
	#[Route('/order/success/{reference}', name: 'payment_success')]
	public function stripeSuccess($reference, CartService $cartService): Response
	{
		return $this->render('order/success.html.twig', [
			'reference' => $reference,
		]);
	}
	
	//Function to redirect to the error page
	#[Route('/order/error/{reference}', name: 'payment_error')]
	public function stripeError($reference, CartService $cartService): Response
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
				'name' => $product->getGame(),
				'quantity' => $product->getQuantity(),
				'unit_amount' => [
					'value' => $product->getPrice(),
					'currency_code' => 'EUR',
				]
			];
			
			$itemTotal += $product->getPrice() * $product->getQuantity();
		}
		
		$shipping = floatval(number_format($itemTotal / 100 * 20, 2, '.', ''));
		$total = floatval(number_format($itemTotal + $shipping, 2, '.', ''));
		$itemTotal = floatval(number_format($itemTotal, 2, '.', ''));
		
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
		
		$purchase->setPaypalOrderId($response->result->id);
		$this->em->flush();
		
		return new RedirectResponse($approvalLink);
	}
}
