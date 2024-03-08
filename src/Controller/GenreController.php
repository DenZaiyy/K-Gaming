<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Service\BreadCrumbsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GenreController extends AbstractController
{
    /*
     * Méthode permettant d'afficher la liste des genres
     */
    #[Route("/{_locale<%app.supported_locales%>}/genres", name: "genre_list")]
    public function index (EntityManagerInterface $em, BreadCrumbsService $breadCrumbsService): Response
    {
        $genres = $em->getRepository(Genre::class)->findAll();

        $breadCrumbsService->BCGenerate([], [], [], []);

        return $this->render("genre/index.html.twig", ["genres" => $genres,
          "description" => "Liste des genres de jeux vidéo disponibles sur le site K-GAMING.",]);
    }
}
