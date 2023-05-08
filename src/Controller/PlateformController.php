<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
    #[Route('/plateform', name: 'app_plateform')]
    public function index(): Response
    {
        return $this->render('plateform/index.html.twig', [
            'controller_name' => 'PlateformController',
        ]);
    }
}
