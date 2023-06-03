<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Stock;
use App\Service\CallApiService;
use Doctrine\ORM\EntityManagerInterface;
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

		
		return $this->render('game/show.html.twig', [
			'game' => $game,
			'gameStock' => $gameStock,
			'gamePlatform' => $gamePlatform,
		]);
	}

    #[Route('/platform/{platformSlug}/game/{gameSlug}', name: 'app_show_game_platform')]
	public function showGameInPlatform(EntityManagerInterface $em, $platformSlug, $gameSlug): Response
	{
		$game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
        $platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
		$gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($game->getId(), $platform->getId());
		$gameStock = $em->getRepository(Stock::class)->findAvailableGameStockByPlatform($game->getId(), $platform->getId());

		return $this->render('game/show.html.twig', [
			'game' => $game,
			'gamePlatform' => $gamePlatform,
			'gameStock' => $gameStock,
		]);
	}

    #[Route('/getGameInfos/{gameLabel}', name: 'app_get_game_infos')]
    public function getImageIDGame($gameLabel): Response
    {
        return $this->render('components/game/_game_image_id.html.twig', [
            'gameInfos' => $this->callApiService->getCoverByGame($gameLabel)
        ]);
    }

    #[Route('/getGameSummary/{gameLabel}', name: 'app_get_game_summary')]
    public function getSummaryGame($gameLabel): Response
    {
        return $this->render('components/game/_game_summary.html.twig', [
            'gameInfos' => $this->callApiService->getSummaryByGame($gameLabel)
        ]);
    }

    #[Route('/getGameScreenshots/{gameLabel}', name: 'app_get_game_screenshots')]
    public function getScreenshotsGame($gameLabel): Response
    {
        return $this->render('components/game/_game_screenshots.html.twig', [
            'gameInfos' => $this->callApiService->getScreenshotByGame($gameLabel)
        ]);
    }

    #[Route('/getGameVideos/{gameLabel}', name: 'app_get_game_videos')]
    public function getVideosGame($gameLabel): Response
    {
        return $this->render('components/game/_game_videos.html.twig', [
            'gameInfos' => $this->callApiService->getVideosByGame($gameLabel)
        ]);
    }
}
