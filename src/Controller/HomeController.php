<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Plateform;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em): Response
	{
		$tendencies = $em->getRepository(Stock::class)->findGamesInTendencies();
		dd($tendencies);

		return $this->render('home/index.html.twig', [
			'tendencies' => $tendencies,
		]);
	}
}
