<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateform;
use App\Entity\Purchase;
use App\Entity\Rating;
use App\Form\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    #[Route('/rating/{gameSlug}/{platformSlug}', name: 'app_game_rating')]
    public function index(EntityManagerInterface $em, Request $request, $gameSlug, $platformSlug): Response
    {
		$game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);
		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);

	    $user = $this->getUser();

		$rating = $em->getRepository(Rating::class)->findOneBy([
				'user' => $user,
				'game' => $game,
				'platform' => $platform
			]
	    );

		if($rating)
		{
			$this->addFlash('error', 'Vous avez déjà voter pour ce jeu, veuillez modifier votre note directement dans votre profil');
			return $this->redirectToRoute('app_home');
		}

		if(!$user)
		{
			$this->addFlash('error', 'Vous devez être connecté pour noter un jeu');
			return $this->redirectToRoute('app_login');
		}

		$rating = new Rating();
		$ratingForm = $this->createForm(RatingType::class, $rating);
		$ratingForm->handleRequest($request);

		if($ratingForm->isSubmitted() && $ratingForm->isValid())
		{
			if($ratingForm->getData()->getNote() == 0)
			{
				$this->addFlash('error', 'Vous devez mettre une note au jeu');
				return $this->redirectToRoute('app_game_rating', ['gameSlug' => $gameSlug, 'platformSlug' => $platformSlug]);
			}

			$rating->setUser($user);
			$rating->setGame($game);
			$rating->setPlatform($platform);
			$rating->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
			$rating = $ratingForm->getData();

			$em->persist($rating);
			$em->flush();

			return $this->redirectToRoute('app_home');
		}

        return $this->render('rating/index.html.twig', [
            'ratingForm' => $ratingForm->createView(),
	        'game' => $game,
	        'platform' => $platform,
        ]);
    }
}
