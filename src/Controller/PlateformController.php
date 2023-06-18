<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Game;
use App\Entity\Plateform;
use App\Form\SearchForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
	/*
	 * Méthode permettant d'afficher la liste des jeux d'une plateforme en prenant en compte les filtres de recherche en ajax
	 */
	#[Route('/platform/{platformSlug}', name: 'app_game_platform')]
	public function index(EntityManagerInterface $em, Request $request, $platformSlug): Response
	{
		$data = new SearchData(); // Création d'un objet SearchData
		$data->page = $request->get('page', 1); // Récupération de la page en cours sinon 1 par défaut
		$form = $this->createForm(SearchForm::class, $data);
		$form->handleRequest($request);

		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]); // Récupération de la plateforme grâce au slug
		$gamesAvailable = $em->getRepository(Game::class)->findGamesInPlatform($platform->getId()); // Récupération des jeux disponibles dans la plateforme

		$games = $em->getRepository(Game::class)->findSearch($data, $platform); // Récupération des jeux en fonction des filtres de recherche
		[$min, $max] = $em->getRepository(Game::class)->findMinMax($data, $platform); // Récupération du prix minimum et maximum des jeux en fonction des filtres de recherche

		/*
		 * Si la requête est en ajax, on retourne un objet JsonResponse avec les données suivantes :
		 * - content : le contenu de la vue _games.html.twig
		 * - sorting : le contenu de la vue _sorting.html.twig
		 * - pagination : le contenu de la vue _pagination.html.twig
		 * - pages : le nombre de pages
		 * - min : le prix minimum
		 * - max : le prix maximum
		 */
		if ($request->get('ajax')) {
			return new JsonResponse([
				'content' => $this->renderView('game/platform/_games.html.twig', ['games' => $games, 'platform' => $platform]),
				'sorting' => $this->renderView('game/platform/_sorting.html.twig', ['games' => $games, 'platform' => $platform]),
				'pagination' => $this->renderView('game/platform/_pagination.html.twig', ['games' => $games, 'platform' => $platform]),
				'pages' => ceil($games->getTotalItemCount() / $games->getItemNumberPerPage()),
				'min' => $min,
				'max' => $max,
			]);
		}

		return $this->render('game/platform/index.html.twig', [
			'form' => $form->createView(),
			'gameAvailable' => $gamesAvailable,
			'games' => $games,
			'platform' => $platform,
			'min' => $min,
			'max' => $max,
		]);
	}
}
