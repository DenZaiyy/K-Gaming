<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
    #[Route('/platform/{platformID}', name: 'app_game_platform')]
    public function index(EntityManagerInterface $em, $platformID): Response
    {
        $gamePlatform = $em->getRepository(Game::class)->findGamesInPlatform($platformID);
//        dd($gamePlatform);
        $platform = $em->getRepository(Plateform::class)->findOneBy(['id' => $platformID]);

        return $this->render('platform/index.html.twig', [
            'games' => $gamePlatform,
            'platform' => $platform,
        ]);
    }
}
