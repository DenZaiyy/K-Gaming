<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Service\CallApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	public function __construct(private CallApiService $callApiService)
	{
	}

	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em): Response
	{
		$tendencies = $em->getRepository(Stock::class)->findGamesInTendencies();

		return $this->render('home/index.html.twig', [
			'tendencies' => $tendencies,
		]);
	}

	#[Route('/getGameInfos/{gameLabel}', name: 'app_get_game_infos')]
	public function getInfosGame($gameLabel): Response
	{
		return $this->render('home/_game_infos.html.twig', [
			'gameInfos' => $this->callApiService->callApi($gameLabel),
			'gameName' => $gameLabel,
		]);
	}
}
