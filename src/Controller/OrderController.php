<?php

namespace App\Controller;

use App\Entity\Purchase;
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
            'userAddress' => $this->getUser()->getAddresses(),
            'cartTotal' => $cartService->getTotalCart(),
        ]);
    }

    #[Route('/order/verify', name: 'order_prepare', methods: ['POST'])]
    public function prepareOrder(Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
//        dd($form->getData());

        if($form->isSubmitted() && $form->isValid())
        {
            $currentDate = new \DateTime('now');
            $delivery = $form->get('addresses')->getData();
            /*$deliveryForOrder = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $deliveryForOrder .= '<br/>' . $delivery->getAddress();
            $deliveryForOrder .= '<br/>' . $delivery->getCp() . ' ' . $delivery->getCity();*/

            $reference = $currentDate->format('dmY') . '-' . uniqid();

            $purchase = new Purchase();
            $purchase->setUser($this->getUser());
            $purchase->setReference($reference);
            $purchase->setCreatedAt($currentDate);
            $purchase->setAddress($delivery);
            $purchase->setIsPaid(0);
            $purchase->setMethod('stripe');

            $this->em->persist($purchase);

            dd($purchase);
        }

        return $this->render('order/recap.html.twig');
    }
}
