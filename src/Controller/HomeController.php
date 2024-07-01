<?php

namespace App\Controller;

use App\Controller\Newsletter\SubscriptionController;
use App\Entity\Category;
use App\Entity\Genre;
use App\Entity\Plateform;
use App\Entity\Stock;
use App\Service\CallApiService;
use App\Service\CartService;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route("/")]
    public function indexNoLocale(Request $request): Response
    {
        if (!$request->cookies->get("_locale")) {
            $locale = $request->getDefaultLocale();
        } else {
            $locale = $request->cookies->get("_locale");
        }

        $response = $this->redirectToRoute("app_home", ["_locale" => $locale]);
        $response->headers->setCookie(new Cookie("_locale", $request->getLocale()));
        $response->send();
        return $response;
    }

    /*
     * Méthode permettant d'afficher la page d'accueil du site avec les informations nécessaires tel que les jeux en tendances, les précommandes et les genres
     */
    #[Route("/{_locale<%app.supported_locales%>}", name: "app_home")]
    public function index(EntityManagerInterface $em, Request $request, CallApiService $callApiService): Response
    {
        $cookies = $request->cookies;
        $screenWidth = $cookies->get("sw");

        if ($screenWidth < 768) {
            $resultPerPage = 2;
        } else if ($screenWidth < 992) {
            $resultPerPage = 4;
        } else {
            $resultPerPage = 6;
        }

        $tendencies = $em->getRepository(Stock::class)->findGamesInTendencies(3);
        $preorders = $callApiService->getInfosByGames(["preorders" => new DateTime('now', new DateTimeZone('Europe/Paris'))]);
        $genres = $em->getRepository(Genre::class)->findGenres($resultPerPage);


        $newsletter = $this->forward(SubscriptionController::class . "::subscribe", ["request" => $request]);

        return $this->render("home/index.html.twig", [
            "tendencies" => $tendencies,
            "preorders" => $preorders,
            "genres" => $genres,
            "newsletter" => $newsletter,
            "description" => "Bienvenue sur le site K-GAMING, le site de vente de jeux vidéo en ligne. Retrouvez les dernières nouveautés et les meilleurs jeux du moment."
        ]);
    }

    /*
     * Méthode permettant d'afficher la barre de navigation du site en composant twig en lui passant les catégories et les plateformes
     */
    public function navBar(EntityManagerInterface $em, CartService $cartService): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();
        $platforms = $em->getRepository(Plateform::class)->findAll();

        return $this->render("components/_header.html.twig", [
            "categories" => $categories,
            "platforms" => $platforms,
            "cartTotal" => $cartService->getTotalCart()
        ]);
    }
}
