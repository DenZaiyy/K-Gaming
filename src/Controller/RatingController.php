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

            $this->addFlash('success', 'La note a bien été ajoutée');
			return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
		}

        return $this->render('rating/index.html.twig', [
            'ratingForm' => $ratingForm->createView(),
	        'game' => $game,
	        'platform' => $platform,
            'edit' => false,
            'ratingValue' => 0
        ]);
    }

    #[Route('/rating/edit/{gameSlug}/{platformSlug}', name: 'rating_edit')]
    public function edit(EntityManagerInterface $em, Request $request, $gameSlug, $platformSlug): Response
    {
        $platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
        $game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);

        $rating = $em->getRepository(Rating::class)->findOneBy([
            'user' => $this->getUser(),
            'game' => $game,
            'platform' => $platform
        ]);

        if ($this->getUser()->getId() != $rating->getUser()->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier cette note');
            return $this->redirectToRoute('app_home');
        }

        $ratingForm = $this->createForm(RatingType::class, $rating);
        $ratingForm->handleRequest($request);

        if ($ratingForm->isSubmitted() && $ratingForm->isValid()) {
            $rating = $ratingForm->getData();
            $rating->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

            $em->persist($rating);
            $em->flush();

            $this->addFlash('success', 'La note a bien été modifiée');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('rating/index.html.twig', [
            'ratingForm' => $ratingForm->createView(),
            'game' => $game,
            'platform' => $platform,
            'edit' => true,
            'ratingValue' => $rating->getNote()
        ]);
    }

    #[Route('/rating/delete/{gameSlug}/{platformSlug}', name: 'rating_delete')]
    public function delete(EntityManagerInterface $em, $gameSlug, $platformSlug): Response
    {
        $platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]);
        $game = $em->getRepository(Game::class)->findOneBy(['slug' => $gameSlug]);

        $rating = $em->getRepository(Rating::class)->findOneBy([
            'user' => $this->getUser(),
            'game' => $game,
            'platform' => $platform
        ]);

        if ($this->getUser()->getId() != $rating->getUser()->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer cette note');
            return $this->redirectToRoute('user_my_account');
        }

        $em->remove($rating);
        $em->flush();

        $this->addFlash('success', 'La note a bien été supprimée');
        return $this->redirectToRoute('user_my_account');
    }

    #[Route('/ratings', name: 'rating_list')]
    public function ratingsList(EntityManagerInterface $em): Response
    {
        $ratings = $em->getRepository(Rating::class)->findBy([], ['created_at' => 'DESC']);

        return $this->render('rating/list.html.twig', [
            'ratings' => $ratings
        ]);
    }
}
