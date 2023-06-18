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
	/*
	 * Méthode permettant d'afficher la page du formulaire permettant la notation d'un jeu
	 */
    #[Route('/rating/{gameSlug}/{platformSlug}', name: 'app_game_rating')]
    public function index(EntityManagerInterface $em, Request $request, $gameSlug, $platformSlug): Response
    {
		$game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]); // Récupération du jeu grâce au slug
		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]); // Récupération de la plateforme grâce au slug

	    $user = $this->getUser(); // Récupération de l'utilisateur connecté

	    // Vérification si l'utilisateur a déjà voté pour ce jeu
		$rating = $em->getRepository(Rating::class)->findOneBy([
				'user' => $user,
				'game' => $game,
				'platform' => $platform
			]
	    );

		// Si l'utilisateur a déjà voté pour ce jeu, on le redirige vers la page d'accueil
		if($rating)
		{
			$this->addFlash('danger', 'Vous avez déjà voter pour ce jeu, veuillez modifier votre note directement dans votre profil');
			return $this->redirectToRoute('app_home');
		}

		// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
		if(!$user)
		{
			$this->addFlash('danger', 'Vous devez être connecté pour noter un jeu');
			return $this->redirectToRoute('app_login');
		}

		// Création du formulaire de notation
		$rating = new Rating();
		$ratingForm = $this->createForm(RatingType::class, $rating);
		$ratingForm->handleRequest($request);

		if($ratingForm->isSubmitted() && $ratingForm->isValid())
		{
			// Vérification si l'utilisateur à bien mis une note au jeu
			if($ratingForm->getData()->getNote() == 0)
			{
				$this->addFlash('danger', 'Vous devez mettre une note au jeu');
				return $this->redirectToRoute('app_game_rating', ['gameSlug' => $gameSlug, 'platformSlug' => $platformSlug]);
			}

			$rating->setUser($user); // Ajout de l'utilisateur à la notation
			$rating->setGame($game); // Ajout du jeu à la notation
			$rating->setPlatform($platform); // Ajout de la plateforme à la notation
			$rating = $ratingForm->getData();// Récupération des données du formulaire

			$em->persist($rating); // Sauvegarde de la notation
			$em->flush(); // Enregistrement de la notation

			return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
		}

        return $this->render('rating/index.html.twig', [
            'ratingForm' => $ratingForm->createView(),
	        'game' => $game,
	        'platform' => $platform,
        ]);
    }
}
