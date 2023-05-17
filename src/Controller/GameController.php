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

    #[Route('/game/{gameID}', name: 'app_show_game')]
    public function index(EntityManagerInterface $em, $gameID): Response
    {
        $game = $em->getRepository(Game::class)->findOneBy(['id' => $gameID]);
        $gameStock = $em->getRepository(Stock::class)->findStockByGameID($gameID);
//        dd($gameStock);

        return $this->render('game/show.html.twig', [
            'game' => $game,
            'gameStock' => $gameStock,
            'platform' => null,
        ]);
    }

    #[Route('/platform/{platformID}/game/{gameID}', name: 'app_show_game_platform')]
    public function showGameInPlatform(EntityManagerInterface $em, $gameID, $platformID): Response
    {
        $game = $em->getRepository(Game::class)->findOneBy(['id' => $gameID]);
        $platform = $em->getRepository(Plateform::class)->findOneBy(['id' => $platformID]);
//        dd($platform);
        $gamePlatform = $em->getRepository(Game::class)->findOneGameInPlatform($gameID, $platformID);
        $gameStock = $em->getRepository(Stock::class)->findAvailableGameStockByPlatform($gameID, $platformID);

        return $this->render('game/show.html.twig', [
            'game' => $game,
            'platform' => $platform,
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
