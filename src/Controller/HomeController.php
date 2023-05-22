<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\Stock;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em): Response
	{
		$tendencies = $em->getRepository(Stock::class)->findGamesInTendencies();
		$preorders = $em->getRepository(Game::class)->findGamesInPreOrders();
		$genres = $em->getRepository(Genre::class)->findGenres();

		return $this->render('home/index.html.twig', [
			'tendencies' => $tendencies,
			'preorders' => $preorders,
			'genres' => $genres
		]);
	}

	#[Route('/navBar', name: 'app_nav_bar')]
	public function navBar(EntityManagerInterface $em, CartService $cartService): Response
	{
		$categories = $em->getRepository(Category::class)->findAll();
		$plateforms = $em->getRepository(Plateform::class)->findAll();

		return $this->render('components/_header.html.twig', [
			'categories' => $categories,
			'plateforms' => $plateforms,
            'cartTotal' => $cartService->getTotalCart()
		]);
	}
}
