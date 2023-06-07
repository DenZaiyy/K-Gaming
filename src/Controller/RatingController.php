<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Rating;
use App\Form\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    #[Route('/rating/{gameId}/{platformId}', name: 'app_game_rating')]
    public function index(EntityManagerInterface $em, Request $request, Rating $rating = null, $gameId, $platformId): Response
    {
		$game = $em->getRepository(Game::class)->findOneBy(['id' => $gameId]);
		$platform = $em->getRepository(Plateform::class)->findOneBy(['id' => $platformId]);

		$rating = new Rating();
		$ratingForm = $this->createForm(RatingType::class, $rating);
		$ratingForm->handleRequest($request);

		if($ratingForm->isSubmitted() && $ratingForm->isValid())
		{
			$rating->setUser($this->getUser());
			$rating->setGame($game);
			$rating->setPlatform($platform);
			$rating = $ratingForm->getData();

			$em->persist($rating);
			$em->flush();

			return $this->redirectToRoute('app_home');
		}

        return $this->render('rating/index.html.twig', [
            'ratingForm' => $ratingForm->createView(),
        ]);
    }
}
