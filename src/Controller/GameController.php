<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Stock;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    #[Route('/game/{gameID}', name: 'app_show_game')]
    public function index(EntityManagerInterface $em, $gameID): Response
    {
        $game = $em->getRepository(Game::class)->findOneBy(['id' => $gameID]);

        $gameStock = $em->getRepository(Stock::class)->findStockGameByPlateform($gameID, 1);
        // dd($gameStock);
        // $gamePlateforms = $em->getRepository(Plateform::class)->findPlateformsByGameID($gameID);

        // dd($gameStock);
        return $this->render('game/show.html.twig', [
            'game' => $game,
            'gameStock' => $gameStock,
            // 'gamePlateforms' => $gamePlateforms
        ]);
    }

    public function plateformStock(EntityManagerInterface $em, $gameID, $plateformID): Response
    {
        $gameStock = $em->getRepository(Stock::class)->findStockGameByPlateform($gameID, $plateformID);

        return $this->render('game/plateformStock.html.twig', [
            'gameStock' => $gameStock
        ]);
    }
}
