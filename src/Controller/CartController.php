<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
	
	#[Route('/cart', name: 'app_cart_index')]
	public function index(CartService $cartService): Response
	{
		return $this->render('cart/index.html.twig', [
			'cart' => $cartService->getTotal(),
			'cartTotal' => $cartService->getTotalCart(),
		]);
	}
	
	#[Route('/cart/add/game/{id<\d+>}/platform/{idPlatform<\d+>}', name: 'app_add_cart')]
	public function addToCart(CartService $cartService, int $id, int $idPlatform): Response
	{
		$cartService->addToCart($id, $idPlatform);
		
		return $this->redirectToRoute('app_show_game_platform', [
			'gameID' => $id,
			'platformID' => $idPlatform
		]);
	}
	
	#[Route('/cart/buyNow/game/{id<\d+>}/platform/{idPlatform<\d+>}', name: 'app_buy_now')]
	public function buyNow(CartService $cartService, int $id, int $idPlatform): Response
	{
		$cartService->addToCart($id, $idPlatform);
		
		return $this->redirectToRoute('order_create');
	}
	
	#[Route('/cart/remove/{id<\d+>}/platform/{idPlatform<\d+>}', name: 'app_remove_cart')]
	public function removeToCart(CartService $cartService, int $id, int $idPlatform): Response
	{
		$cartService->removeToCart($id, $idPlatform);
		
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
