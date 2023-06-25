<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/404', name: 'app_404')]
    public function accessDenied(): Response
    {
        $this->addFlash('danger', 'La page demandée n\'existe pas');
        return $this->render('security/exception/404.html.twig');
    }
}
