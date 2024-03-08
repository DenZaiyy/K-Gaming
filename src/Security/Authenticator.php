<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class Authenticator extends AbstractLoginFormAuthenticator
{
  use TargetPathTrait;

  public const LOGIN_ROUTE = "app_login";

  public function __construct(
    private readonly UrlGeneratorInterface $urlGenerator,
    private readonly UserRepository $userRepository
  ) {
  }

  public function authenticate(Request $request): Passport
  {
    // utilisation de request pour récupérer les données du formulaire
    $username = $request->request->get("username", "");
    // On récupère la valeur du champ caché
    $honeypot = $request->request->get("_hp_protect", "");

    $user = $this->userRepository->findOneBy(["username" => $username]);

    if (!$user) {
      throw new CustomUserMessageAuthenticationException(
        'Nom d\'utilisateur ou mot de passe incorrect.'
      );
    }

    if (!$user->isVerified()) {
      throw new CustomUserMessageAuthenticationException(
        "Veuillez vérifier votre adresse mail avant de vous connecter."
      );
    }

    // Si le champ n'est pas vide, on est en présence d'un bot
    if (!empty($honeypot)) {
      throw new CustomUserMessageAuthenticationException(
        "Le formulaire n'a pas été soumis correctement."
      );
    }

    // On retourne un objet Passport qui contient les informations de connexion
    return new Passport(
      new UserBadge($username),
      // On récupère le mot de passe dans la requête
      new PasswordCredentials($request->request->get("password", "")),
      [
        // Cette classe permet de vérifier le jeton CSRF (Cross-Site Request Forgery)
        new CsrfTokenBadge(
          "authenticate",
          $request->request->get("_csrf_token")
        ),
        // Cette classe permet de gérer la case à cocher "Se souvenir de moi"
        new RememberMeBadge(),
      ]
    );
  }

  public function onAuthenticationSuccess(
    Request $request,
    TokenInterface $token,
    string $firewallName
  ): ?Response {
    if (
      $targetPath = $this->getTargetPath($request->getSession(), $firewallName)
    ) {
      return new RedirectResponse($targetPath);
    }

    // For example:
    return new RedirectResponse($this->urlGenerator->generate("app_home"));
    // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
  }

  protected function getLoginUrl(Request $request): string
  {
    return $this->urlGenerator->generate(self::LOGIN_ROUTE);
  }
}
