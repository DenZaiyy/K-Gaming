<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Purchase;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route('/order/create-session-stripe/{reference}', name: 'app_stripe_checkout')]
    public function stripeCheckout($reference): RedirectResponse
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
                'app_stripe_success',
                ['reference' => $purchase->getReference()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->urlGenerator->generate(
                'app_stripe_error',
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

    #[Route('/order/success/{reference}', name: 'app_stripe_success')]
    public function stripeSuccess($reference, CartService $cartService): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/error/{reference}', name: 'app_stripe_error')]
    public function stripeError($reference, CartService $cartService): Response
    {
        return $this->render('order/error.html.twig');
    }
}
