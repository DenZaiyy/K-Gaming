<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/profil', name: 'user_my_account')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profil/my-order', name: 'user_my_order')]
    public function myOrder(): Response
    {
        $purchase = $this->getUser()->getPurchase();

        return $this->render('user/my-order.html.twig', [
            'purchases' => $purchase,
        ]);
    }

    #[Route('/profil/add-address', name: 'user_add_address')]
    public function addAddress(Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            $this->addFlash('success', 'Votre adresse a bien été ajoutée');
            return $this->redirectToRoute('user_my_account');
        }

        return $this->render('user/address/add.html.twig', [
            'formAddAddress' => $form->createView(),
        ]);
    }
}
