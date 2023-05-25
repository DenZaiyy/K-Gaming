<?php

namespace App\Controller;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/order/create-session-stripe/{purchaseID}', name: 'app_stripe_checkout')]
    public function stripeCheckout($purchaseID): RedirectResponse
    {
        $order = $this->em->getRepository(Purchase::class)->findOneBy(['id' => $purchaseID]);
        dd($order);


        Stripe::setApiKey($this->getParameter('app.stripe_private_key'));

        $checkout_session = Session::create([
            'line_items' => [[
                'price' => '{{PRICE_ID}}',
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        return new RedirectResponse($checkout_session->url, 303);
    }

    #[Route('/order/success/{reference}', name: 'app_stripe_success')]
    public function stripeSuccess($reference): Response
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/error/{reference}', name: 'app_stripe_error')]
    public function stripeError($reference): Response
    {
        return $this->render('order/success.html.twig');
    }
}
