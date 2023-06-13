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
	#[Route('/platform/{platformSlug}', name: 'app_game_platform')]
	public function index(EntityManagerInterface $em, Request $request, $platformSlug): Response
	{
		$data = new SearchData();
		$data->page = $request->get('page', 1);
		$form = $this->createForm(SearchForm::class, $data);
		$form->handleRequest($request);

		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
		$gamesAvailable = $em->getRepository(Game::class)->findGamesInPlatform($platform->getId());

		$games = $em->getRepository(Game::class)->findSearch($data, $platform);
		[$min, $max] = $em->getRepository(Game::class)->findMinMax($data, $platform);

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
