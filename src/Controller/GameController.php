<?php

namespace App\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game/{gameID}', name: 'app_show_game')]
    public function index(EntityManagerInterface $em, $gameID): Response
    {
		$game = $em->getRepository(Game::class)->findOneBy(['id' => $gameID]);
        return $this->render('game/show.html.twig', [
			'game' => $game,
        ]);
    }
}
