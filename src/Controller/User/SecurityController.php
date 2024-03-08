<?php

namespace App\Controller\User;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: "/{_locale<%app.supported_locales%>}/login", name: "app_login")]
    public function login (AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash("danger", "Vous êtes déjà connecté !");
            return $this->redirectToRoute("app_home");
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render("security/login.html.twig", ["last_username" => $lastUsername,
          "error" => $error,
          "description" => "Connectez-vous pour accéder à votre espace personnel.",]);
    }

    #[Route(path: "/{_locale<%app.supported_locales%>}/logout", name: "app_logout")]
    public function logout (): void
    {
        throw new LogicException(
          "This method can be blank - it will be intercepted by the logout key on your firewall."
        );
    }
}
