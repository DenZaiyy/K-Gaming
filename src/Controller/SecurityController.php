<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/404', name: 'app_404')]
    public function accessDenied(): Response
    {
        $this->addFlash('danger', 'La page demandée n\'existe pas');
        return $this->render('security/exception/404.html.twig', [
			'description' => ''
        ]);
    }
}
