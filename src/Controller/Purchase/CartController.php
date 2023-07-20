<?php

namespace App\Controller\Purchase;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	/*
	 * La fonction index permet d'afficher le panier et récupérer les données du panier grâce au service CartService
	 */
	#[Route('/cart', name: 'app_cart_index')]
	public function index(CartService $cartService): Response
	{
		return $this->render('order/cart/index.html.twig', [
			'cart' => $cartService->getTotal(),
			'cartTotal' => $cartService->getTotalCart(),
		]);
	}

	/*
	 * La fonction addToCart permet d'ajouter un jeu au panier
	 */
	#[Route('/cart/add/{platformSlug}/{gameSlug}', name: 'app_add_cart')]
	public function addToCart(CartService $cartService, $platformSlug, $gameSlug): Response
	{
        // Utilisation du service CartService pour ajouter un jeu au panier en lui renseignant le slug du jeu et de la plateforme
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('app_show_game_platform', [
			'gameSlug' => $gameSlug,
			'platformSlug' => $platformSlug
		]);
	}

	/*
	 * La fonction buyNow permet d'ajouter un jeu au panier et d'aller directement sur la page de commande
	 */
	#[Route('/cart/buyNow/{platformSlug}/{gameSlug}', name: 'app_buy_now')]
	public function buyNow(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('order_create');
	}

	/*
	 * La fonction removeToCart permet de supprimer un jeu du panier
	 */
	#[Route('/cart/remove/{platformSlug}/{gameSlug}', name: 'app_remove_cart')]
	public function removeToCart(CartService $cartService, $platformSlug, $gameSlug): Response
	{
        // Utilisation du service CartService pour supprimer un jeu du panier
        // en lui renseignant le slug du jeu et de la plateforme
		$cartService->removeToCart($gameSlug, $platformSlug);

        // Redirection vers la page du panier
		return $this->redirectToRoute('app_cart_index');
	}

	/*
	 * La fonction quantityChange permet de modifier la quantité d'un jeu dans le panier
	 */
	#[Route('/cart/quantityChange/{id<\d+>}/{qtt<\d+>}', name: 'app_quantity_change_cart')]
	public function quantityChange(CartService $cartService, int $id, int $qtt): Response
	{
		$cartService->changeQtt($id, $qtt);
		
		return $this->redirectToRoute('app_cart_index');
	}

	/*
	 * La fonction removeAll permet de supprimer tous les jeux du panier
	 */
	#[Route('/cart/deleteAll', name: 'app_remove_all_cart')]
	public function removeAll(CartService $cartService): Response
	{
		$cartService->removeCartAll();
		
		return $this->redirectToRoute('app_home');
	}
}
