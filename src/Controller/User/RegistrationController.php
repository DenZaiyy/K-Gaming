<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\MultiAvatars;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
  private EmailVerifier $emailVerifier;

  public function __construct(EmailVerifier $emailVerifier)
  {
    $this->emailVerifier = $emailVerifier;
  }

  /**
   * Fonction permettant de s'inscrire sur le site
   */
  #[Route("/{_locale<%app.supported_locales%>}/register", name: "app_register")]
  public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHashed,
    EntityManagerInterface $entityManager,
    MultiAvatars $avatar
  ): Response {
    if ($this->getUser()) {
      $this->addFlash("danger", "Vous êtes déjà connecté !");
      return $this->redirectToRoute("app_home");
    }

    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $checkMail = $entityManager
        ->getRepository(User::class)
        ->findOneBy(["email" => $form->get("email")->getData()]);

      if ($checkMail) {
        $this->addFlash("danger", "Cette adresse email est déjà utilisée");
        return $this->redirectToRoute("app_register");
      }

      $username = strip_tags($form->get("username")->getData());
      $user->setUsername($username);

      // encode the plain password
      $user->setPassword(
        $userPasswordHashed->hashPassword(
          $user,
          $form->get("plainPassword")->getData()
        )
      );

      $user->setAvatar($avatar->getRandomUserAvatar($user->getUsername()));

      $user->setCreateAt(new DateTimeImmutable());
      $user->setIsBanned(false);

      $entityManager->persist($user);
      $entityManager->flush();

      // generate a signed url and email it to the user
      $this->emailVerifier->sendEmailConfirmation(
        "app_verify_email",
        $user,
        (new TemplatedEmail())
          ->from(new Address("support@k-grischko.fr", "K-Gaming - Support"))
          ->to($user->getEmail())
          ->subject("Confirmer votre email")
          ->htmlTemplate("security/registration/confirmation_email.html.twig")
      );
      // do anything else you need here, like send an email

      return $this->redirectToRoute("app_home");
    }

    return $this->render("security/registration/register.html.twig", [
      "registrationForm" => $form->createView(),
      "description" =>
        "Inscrivez-vous sur K-Gaming et rejoignez une communauté de joueurs !",
    ]);
  }

  /**
   * Fonction permettant de vérifier l'email de l'utilisateur pour l'inscription
   */
  #[
    Route(
      "/{_locale<%app.supported_locales%>}/verify/email",
      name: "app_verify_email"
    )
  ]
  public function verifyUserEmail(
    Request $request,
    TranslatorInterface $translator,
    UserRepository $userRepository
  ): Response {
    //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    $id = $request->query->get("id"); // retrieve the user id from the url
    $user = $userRepository->find($id);

    // Verify the user id exists and is not null
    if (null === $id || null === $user) {
      return $this->redirectToRoute("app_home");
    }

    // validate email confirmation link, sets User::isVerified=true and persists
    try {
      $this->emailVerifier->handleEmailConfirmation($request, $user);
    } catch (VerifyEmailExceptionInterface $exception) {
      $this->addFlash(
        "verify_email_error",
        $translator->trans($exception->getReason(), [], "VerifyEmailBundle")
      );

      return $this->redirectToRoute("app_register");
    }

    $this->addFlash("success", "Votre adresse e-mail a été vérifiée.");

    return $this->redirectToRoute("app_home");
  }
}
