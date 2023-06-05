<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
    #[Route('/platform/{platformSlug}', name: 'app_game_platform')]
    public function index(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator, $platformSlug): Response
    {
        $platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
        $games = $em->getRepository(Game::class)->findGamesInPlatform($platform->getId());

        $pagination = $paginator->paginate(
            $em->getRepository(Game::class)->findGamesInPlatformPagination($platform->getId()),
            $request->query->get('page', 1),
            3
        );

        $pagination->setCustomParameters([
            'align' => 'center',
            'size' => 'small',
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);

        return $this->render('game/platform/index.html.twig', [
            'gameAvailable' => $games,
            'games' => $pagination,
            'platform' => $platform,
        ]);
    }
}
