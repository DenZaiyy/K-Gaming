<?php

namespace App\Controller\Purchase;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	
	#[Route('/cart', name: 'app_cart_index')]
	public function index(CartService $cartService): Response
	{
		return $this->render('order/cart/index.html.twig', [
			'cart' => $cartService->getTotal(),
			'cartTotal' => $cartService->getTotalCart(),
		]);
	}
	
	#[Route('/cart/add/{platformSlug}/{gameSlug}', name: 'app_add_cart')]
	public function addToCart(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('app_show_game_platform', [
			'gameSlug' => $gameSlug,
			'platformSlug' => $platformSlug
		]);
	}
	
	#[Route('/cart/buyNow/{platformSlug}/{gameSlug}', name: 'app_buy_now')]
	public function buyNow(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		$cartService->addToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('order_create');
	}
	
	#[Route('/cart/remove/{platformSlug}/{gameSlug}', name: 'app_remove_cart')]
	public function removeToCart(CartService $cartService, $platformSlug, $gameSlug): Response
	{
		$cartService->removeToCart($gameSlug, $platformSlug);
		
		return $this->redirectToRoute('app_cart_index');
	}
	
	#[Route('/cart/quantityChange/{id<\d+>}/{qtt<\d+>}', name: 'app_quantity_change_cart')]
	public function quantityChange(CartService $cartService, int $id, int $qtt): Response
	{
		$cartService->changeQtt($id, $qtt);
		
		return $this->redirectToRoute('app_cart_index');
	}
	
	
	#[Route('/cart/deleteAll', name: 'app_remove_all_cart')]
	public function removeAll(CartService $cartService): Response
	{
		$cartService->removeCartAll();
		
		return $this->redirectToRoute('app_home');
	}
}
