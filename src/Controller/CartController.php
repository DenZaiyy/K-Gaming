<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    #[Route('/cart', name: 'app_cart_index')]
    public function index(CartService $cartService) : Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal()
        ]);
    }

    #[Route('/cart/add/game/{id<\d+>}/platform/{idPlatform<\d+>}', name: 'app_add_cart')]
    public function addToCart(CartService $cartService, int $id, int $idPlatform): Response
    {
        $cartService->addToCart($id, $idPlatform);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/remove/{id<\d+>}', name: 'app_remove_cart')]
    public function removeToCart(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/increase/{id<\d+>}', name: 'app_increase_cart')]
    public function increase(CartService $cartService, int $id): Response
    {
        $cartService->increase($id);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/decrease/{id<\d+>}', name: 'app_decrease_cart')]
    public function decrease(CartService $cartService, int $id): Response
    {
        $cartService->decrease($id);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/deleteAll', name: 'app_remove_all_cart')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();

        return $this->redirectToRoute('app_home');
    }
}
