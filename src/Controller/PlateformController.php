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
    #[Route('/plateform/{plateformID}', name: 'app_game_plateform')]
    public function index(EntityManagerInterface $em, $plateformID): Response
    {
        $gamePlateform = $em->getRepository(Game::class)->findGamesInPlatform($plateformID);

        return $this->render('plateform/index.html.twig', [
            'gamePlateform' => '$gamePlateform',
        ]);
    }
}
