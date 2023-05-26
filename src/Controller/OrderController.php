<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\RecapDetails;
use App\Form\OrderType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/order/create', name: 'order_create')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $cartService->getTotal(),
            'cartTotal' => $cartService->getTotalCart(),
        ]);
    }

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
            $currentDate = new \DateTime('now');
            $delivery = $form->get('addresses')->getData();

            $deliveryForPurchase = $delivery->getAddress() . '</br>';
            $deliveryForPurchase .= $delivery->getCp() . ' - ' . $delivery->getCity();

            $reference = $currentDate->format('dmY') . '-' . uniqid();

            $purchase = new Purchase();
            $purchase->setUser($this->getUser());
            $purchase->setReference($reference);
            $purchase->setCreatedAt($currentDate);
            $purchase->setAddress($delivery);
            $purchase->setDelivery($deliveryForPurchase);
            $purchase->setUserFullName($delivery->getFirstname() . ' ' . $delivery->getLastname());
            $purchase->setIsPaid(0);
            $purchase->setMethod('stripe');

            $this->em->persist($purchase);

            foreach ($cartService->getTotal() as $product) {
                $recapDetails = new RecapDetails();

                $recapDetails->setOrderProduct($purchase);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['game']->getPrice());
                $recapDetails->setGame($product['game']->getLabel());
                $recapDetails->setPlatform($product['platform']->getLabel());
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

        return $this->redirectToRoute('cart_index');
    }
}
