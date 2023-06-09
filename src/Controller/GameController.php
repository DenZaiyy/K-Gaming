<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\Rating;
use App\Entity\Stock;
use App\Service\CallApiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
	public function __construct(private readonly CallApiService $callApiService)
	{
	}

	#[Route('/game/{gameSlug}', name: 'app_show_game')]
	public function showGame(EntityManagerInterface $em, $gameSlug): Response
	{
		$game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
		$gameStock = $em->getRepository(Stock::class)->findStockByGameID($game->getId());
		$gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game->getId(), $gameStock[0]['platform_id']);

		$ratings = $em->getRepository(Rating::class)->findBy(
			['game' => $game->getId()]
		);

		$averageRating = $em->getRepository(Rating::class)->findBy(['game' => $game->getId()]);

		$moyenne = 0;
		if($averageRating)
		{
			$somme = 0;
			foreach ($averageRating as $rate)
			{
				$somme += $rate->getNote();
			}

			$moyenne = $somme / count($averageRating);
		}



		return $this->render('game/show.html.twig', [
			'game' => $game,
			'gameStock' => $gameStock,
			'gamePlatform' => $gamePlatform,
			'ratings' => $ratings,
			'moyenne' => $moyenne,
		]);
	}

	#[Route('/platform/{platformSlug}/game/{gameSlug}', name: 'app_show_game_platform')]
	public function showGameInPlatform(EntityManagerInterface $em, $platformSlug, $gameSlug): Response
	{
		$game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
		$gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game->getId(), $platform->getId());
		$gameStock = $em->getRepository(Stock::class)->findAvailableGameStockByPlatform($game->getId(), $platform->getId());

		$ratings = $em->getRepository(Rating::class)->findBy(
			['game' => $game->getId()]
		);

		$averageRating = $em->getRepository(Rating::class)->findBy(['game' => $game->getId()]);

		$somme = 0;

		foreach ($averageRating as $key => $rate)
		{
			$somme += $rate->getNote();
		}

		$moyenne = $somme / count($averageRating);


		return $this->render('game/show.html.twig', [
			'game' => $game,
			'gamePlatform' => $gamePlatform,
			'gameStock' => $gameStock,
			'ratings' => $ratings,
			'moyenne' => $moyenne,
		]);
	}

	#[Route('/preoder/game', name: 'app_show_preorders')]
	public function showGameInPreorder(EntityManagerInterface $em): Response
	{
		$date = new \DateTime();
		$gamePreorder = $em->getRepository(Game::class)->findGameInPreorder($date);

		return $this->render('game/preOrder/index.html.twig', [
			'gamePreorder' => $gamePreorder,
		]);
	}

	#[Route('/game/genre/{genreSlug}', name: 'app_show_game_genre')]
	public function showGameByGenre(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator, $genreSlug): Response
	{
		$games = $em->getRepository(Game::class)->findGameByGenre($genreSlug);
		$genre = $em->getRepository(Genre::class)->findOneBy(['slug' => $genreSlug]);

		$pagination = $paginator->paginate(
			$em->getRepository(Game::class)->findGameByGenrePagination($genre->getSlug()),
			$request->query->get('page', 1),
			9
		);

		$pagination->setCustomParameters([
			'align' => 'center',
			'size' => 'small',
			'style' => 'bottom',
			'span_class' => 'whatever',
		]);

		return $this->render('game/genre/show.html.twig', [
			'games' => $pagination,
			'gameAvailable' => $games,
			'genre' => $genre,
		]);
	}

	public function getImageIDGame($gameLabel): Response
	{
		return $this->render('components/game/_game_image_id.html.twig', [
			'gameInfos' => $this->callApiService->getCoverByGame($gameLabel)
		]);
	}

	public function getSummaryGame($gameLabel): Response
	{
		return $this->render('components/game/_game_summary.html.twig', [
			'gameInfos' => $this->callApiService->getSummaryByGame($gameLabel)
		]);
	}

	public function getScreenshotsGame($gameLabel): Response
	{
		return $this->render('components/game/_game_screenshots.html.twig', [
			'gameInfos' => $this->callApiService->getScreenshotByGame($gameLabel)
		]);
	}

	public function getVideosGame($gameLabel): Response
	{
		return $this->render('components/game/_game_videos.html.twig', [
			'gameInfos' => $this->callApiService->getVideosByGame($gameLabel)
		]);
	}
}
