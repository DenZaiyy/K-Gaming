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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/*
	 * Méthode permettant d'afficher la page d'accueil du site avec les informations nécessaires tel que les jeux en tendances, les précommandes et les genres
	 */
	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em, Request $request): Response
	{
        $cookies = $request->cookies;
        $screenWidth = $cookies->get('screenWidth');

        if ($screenWidth < 768) {
            $resultPerPage = 2;
        } else if ($screenWidth < 992) {
            $resultPerPage = 4;
        } else {
            $resultPerPage = 6;
        }

		$tendencies = $em->getRepository(Stock::class)->findGamesInTendencies($resultPerPage);
		$preorders = $em->getRepository(Game::class)->findGamesInPreOrders($resultPerPage);
		$genres = $em->getRepository(Genre::class)->findGenres($resultPerPage);

		return $this->render('home/index.html.twig', [
			'tendencies' => $tendencies,
			'preorders' => $preorders,
			'genres' => $genres,
		]);
	}

	/*
	 * Méthode permettant d'afficher la barre de navigation du site en composant twig en lui passant les catégories et les plateformes
	 */
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
