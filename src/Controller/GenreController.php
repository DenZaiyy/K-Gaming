<?php

namespace App\Controller;

use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    #[Route('/genre', name: 'app_genre_list')]
    public function index(EntityManagerInterface $em): Response
    {
		$genres = $em->getRepository(Genre::class)->findAll();

        return $this->render('genre/index.html.twig', [
			'genres' => $genres,
        ]);
    }
}
