<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Game;
use App\Entity\Plateform;
use App\Form\SearchForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
	/*
	 * Méthode permettant d'afficher la liste des jeux d'une plateforme en prenant en compte les filtres de recherche en ajax
	 */
	#[Route('/platform/{platformSlug}', name: 'app_game_platform')]
	public function index(EntityManagerInterface $em, Request $request, $platformSlug): Response
	{
		$data = new SearchData(); // Création d'un objet SearchData
		$data->page = $request->get('page', 1); // Récupération de la page en cours sinon 1 par défaut
		$form = $this->createForm(SearchForm::class, $data);
		$form->handleRequest($request);

        $cookies = $request->cookies;
        $screenWidth = $cookies->get('screenWidth');

        if ($screenWidth < 768) {
            $resultPerPage = 3;
        } else if ($screenWidth < 992) {
            $resultPerPage = 6;
        } else {
            $resultPerPage = 9;
        }

		$platform = $em->getRepository(Plateform::class)->findOneBy(['slug' => $platformSlug]); // Récupération de la plateforme grâce au slug
		$gamesAvailable = $em->getRepository(Game::class)->findGamesInPlatform($platform->getId()); // Récupération des jeux disponibles dans la plateforme

		$games = $em->getRepository(Game::class)->findSearch($data, $platform, $resultPerPage); // Récupération des jeux en fonction des filtres de recherche
		[$min, $max] = $em->getRepository(Game::class)->findMinMax($data, $platform); // Récupération du prix minimum et maximum des jeux en fonction des filtres de recherche

		/*
		 * Si la requête est en ajax, on retourne un objet JsonResponse avec les données suivantes :
		 * - content : le contenu de la vue _games.html.twig
		 * - sorting : le contenu de la vue _sorting.html.twig
		 * - pagination : le contenu de la vue _pagination.html.twig
		 * - pages : le nombre de pages
		 * - min : le prix minimum
		 * - max : le prix maximum
		 */
		if ($request->get('ajax')) {
			return new JsonResponse([
				'content' => $this->renderView('game/platform/_games.html.twig', ['games' => $games, 'platform' => $platform]),
				'sorting' => $this->renderView('game/platform/_sorting.html.twig', ['games' => $games, 'platform' => $platform]),
				'pagination' => $this->renderView('game/platform/_pagination.html.twig', ['games' => $games, 'platform' => $platform]),
				'pages' => ceil($games->getTotalItemCount() / $games->getItemNumberPerPage()),
				'min' => $min,
				'max' => $max,
			]);
		}

		return $this->render('game/platform/index.html.twig', [
			'form' => $form->createView(),
			'gameAvailable' => $gamesAvailable,
			'games' => $games,
			'platform' => $platform,
			'min' => $min,
			'max' => $max,
            'description' => "Liste des jeux de la plateforme " . $platform->getLabel() . " disponibles sur le site K-GAMING."
		]);
	}

    #[Route('/screen-size', name: 'app_screen_size')]
    public function checkWindowSize(Request $request): Response
    {
        $screenWidth = $request->request->get('screenWidth');

        $cookie = new Cookie(
            'screenWidth',    // Cookie name
            $screenWidth,    // Cookie value
            (new \DateTime('now'))->modify('+7 days'),   // Expires after 7 days
            '/',                // Path
            "127.0.0.1",               // Domain
            $request->getScheme() === "https",              // Secure
            true,                // HttpOnly
            true,                 // Raw
            'Lax'            // SameSite attribute
        );

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->send();

        $data = [
            'screenWidth' => $screenWidth
        ];

        return $this->json($data);
    }
}
