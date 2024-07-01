<?php

namespace App\Controller\Purchase;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/{_locale<%app.supported_locales%>}/cart', name: 'cart_')]
class CartController extends AbstractController
{
	/*
	 * La fonction index permet d'afficher le panier et récupérer les données du panier grâce au service CartService
	 */
	#[Route('', name: 'index')]
	public function index(CartService $cartService): Response
	{
		return $this->render('order/cart/index.html.twig', [
			'cart' => $cartService->getTotal(),
			'cartTotal' => $cartService->getTotalCart(),
			'description' => "Récupérer la liste de vos produits dans le panier"
		]);
	}
	
	/*
	 * La fonction addToCart permet d'ajouter un jeu au panier
	 */
	#[Route('/add/{categoryLabel}/{platformSlug}/{gameSlug}', name: 'add')]
	public function addToCart(CartService $cartService, $categoryLabel, $platformSlug, $gameSlug): Response
	{
		// Utilisation du service CartService pour ajouter un jeu au panier en lui renseignant le slug du jeu et de la plateforme
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('app_show_game_platform', [
			'gameSlug' => $gameSlug,
			'platformSlug' => $platformSlug,
			'categoryLabel' => strtolower($categoryLabel)
		]);
	}
	
	/*
	 * La fonction buyNow permet d'ajouter un jeu au panier et d'aller directement sur la page de commande
	 */
	#[Route('/buyNow/{platformSlug}/{gameSlug}', name: 'buy_now')]
	public function buyNow(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('order_create');
	}
	
	/*
	 * La fonction removeToCart permet de supprimer un jeu du panier
	 */
	#[Route('/remove/{platformSlug}/{gameSlug}', name: 'remove')]
	public function removeToCart(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		// Utilisation du service CartService pour supprimer un jeu du panier
		// en lui renseignant le slug du jeu et de la plateforme
		$cartService->removeToCart($gameSlug, $platformSlug);
		
		// Redirection vers la page du panier
		return $this->redirectToRoute('cart_index');
	}
	
	/*
	 * La fonction quantityChange permet de modifier la quantité d'un jeu dans le panier
	 */
	#[Route('/quantityChange/{id<\d+>}/{qtt<\d+>}', name: 'quantity_change')]
	public function quantityChange(CartService $cartService, int $id, int $qtt): Response
	{
		$cartService->changeQtt($id, $qtt);
		
		return $this->redirectToRoute('cart_index');
	}
	
	/*
	 * La fonction removeAll permet de supprimer tous les jeux du panier
	 */
	#[Route('/deleteAll', name: 'remove_all')]
	public function removeAll(CartService $cartService): Response
	{
		$cartService->removeCartAll();
		$this->addFlash('success', 'Votre panier a bien été vidé !');
		
		return $this->redirectToRoute('app_home');
	}
}
