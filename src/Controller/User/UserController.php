<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\Purchase;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\User\UpdateEmailType;
use App\Form\User\UpdatePasswordType;
use App\Form\User\UpdateUsernameType;
use App\Security\EmailVerifier;
use App\Service\MultiAvatars;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}/profil', name: 'user_')]
class UserController extends AbstractController
{
	/*
	 * Constructeur permettant d'instancier l'entityManager et l'utiliser dans les fonctions
	 */
    public function __construct(private EntityManagerInterface $em, private EmailVerifier $emailVerifier)
    {
    }

    #[Route('', name: 'my_account')]
    public function index(Request $request, UserPasswordHasherInterface $hasher, MultiAvatars $multiAvatars): Response
    {
        $user = $this->getUser();
	    $currentUser = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
        $currentDate = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $userAddress = $this->em->getRepository(Address::class)->findBy(['user' => $currentUser]);

        $avatars = $multiAvatars->getEightRandomlyAvatar();

		$usernameForm = $this->createForm(UpdateUsernameType::class, $user);
        $passwordForm = $this->createForm(UpdatePasswordType::class, $user);
        $emailForm = $this->createForm(UpdateEmailType::class, $user);

		$usernameForm->handleRequest($request);
		if ($usernameForm->isSubmitted() && $usernameForm->isValid())
		{
			$check = $this->em->getRepository(User::class)->findOneBy(['username' => $usernameForm->getData()->getUsername()]);
			if($check)
			{
				$this->addFlash('danger', 'Ce nom d\'utilisateur est déjà utilisé');
				return $this->redirectToRoute('user_my_account');
			}

			$currentUser->setUpdatedAt($currentDate);

            // On récupère le nom d'utilisateur et on le nettoie pour éviter les failles XSS
            $username = strip_tags($usernameForm->get('username')->getData());
            // On met à jour le nom d'utilisateur
			$currentUser->setUsername($username);

            // On met à jour les informations de l'utilisateur
			$this->em->persist($currentUser);
			$this->em->flush();

			$this->addFlash('success', 'Votre nom d\'utilisateur a bien été modifié');
			return $this->redirectToRoute('user_my_account');
		}

        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid())
        {
            $check = $this->em->getRepository(User::class)->findOneBy(['email' => $emailForm->getData()->getEmail()]);
            if($check)
            {
                $this->addFlash('danger', 'Cet adresse email est déjà utilisé');
                return $this->redirectToRoute('user_my_account');
            }

            $currentUser->setUpdatedAt($currentDate);
            $currentUser->setIsVerified(false);
            $email = strip_tags($emailForm->get('email')->getData());
            $currentUser->setEmail($email);

            $this->em->persist($currentUser);
            $this->em->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $currentUser,
                (new TemplatedEmail())
                    ->from(new \Symfony\Component\Mime\Address('support@k-grischko.fr', 'K-Gaming - Support'))
                    ->to($currentUser->getEmail())
                    ->subject('Confirmer votre nouvelle adresse email')
                    ->htmlTemplate('security/user/user_email_update.html.twig')
            );

            $this->addFlash('success', 'Votre adresse email a bien été modifié, vous allez recevoir un email de confirmation');
            return $this->redirectToRoute('user_my_account');
        }

		$passwordForm->handleRequest($request);
		if ($passwordForm->isSubmitted() && $passwordForm->isValid())
		{
            if ($hasher->isPasswordValid($currentUser, $passwordForm->get('currentPassword')->getData()))
            {
                $currentUser->setUpdatedAt($currentDate);
                $currentUser->setPassword($hasher->hashPassword($currentUser, $passwordForm->get('plainPassword')->getData()));

                $this->em->persist($currentUser);
                $this->em->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('user_my_account');
            }
            else
            {
                $this->addFlash('danger', 'Votre mot de passe actuel est incorrect');
                return $this->redirectToRoute('user_my_account');
            }
		}

        return $this->render('security/user/index.html.twig', [
            'user' => $user,
			'avatars' => $avatars,
	        'usernameForm' => $usernameForm->createView(),
            'emailForm' => $emailForm->createView(),
	        'passwordForm' => $passwordForm->createView(),
            'user_address' => $userAddress,
            'description' => "Accéder à votre compte et modifier vos informations personnelles"
        ]);
    }

    #[Route('/update-avatar/{image}', name: 'update_avatar')]
    public function updateUserAvatar($image, MultiAvatars $multiAvatars) : Response
    {
        $user = $this->getUser();
        $currentUser = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

        if($currentUser)
        {
            $currentUser->setAvatar($multiAvatars->setAvatarUrl($image));
            $currentUser->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $this->em->persist($currentUser);
            $this->em->flush();
            $this->addFlash('success', 'Votre avatar a bien été modifié');
        } else {
            $this->addFlash('danger', 'Une erreur est survenue');
            return $this->redirectToRoute('user_my_account');
        }

        return $this->redirectToRoute('user_my_account');
    }

	/**
	 * Fonction permettant d'afficher la liste des commandes effectuées par l'utilisateur connecté
	 */
    #[Route('/my-order', name: 'my_order')]
    public function myOrder(): Response
    {
        $purchases = $this->em->getRepository(Purchase::class)->findBy(
			['user' => $this->getUser()],
			['created_at' => 'DESC']
        );

        return $this->render('security/user/my-order.html.twig', [
            'purchases' => $purchases,
            'user' => $this->getUser(),
            'description' => "Accéder à l'historique de vos commandes passées sur K-Gaming"
        ]);
    }

	/**
	 * Fonction permettant d'accéder aux préférences utilisateur, notamment la newsletter et le choix du theme
	 */
    #[Route('/my-preference', name: 'my_preference')]
    public function myPreference(Request $request): Response
    {
		$user = $this->getUser();
		$newsletter = $this->em->getRepository(NewsletterUser::class)->findOneBy(['email' => $user->getEmail()]);
        return $this->render('security/user/my-preference.html.twig', [
			'newsletter' => $newsletter,
            'description' => "Accéder à vos préférences pour la newsletter et le thème du site K-Gaming"
        ]);
    }

	/*
	 * Fonction permettant d'ajouter une nouvelle adresse de facturation à l'utilisateur connecté
	 */
    #[Route('/add-address', name: 'add_address')]
    public function addAddress(Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

	    $url = $request->headers->get('referer');

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $address->setUser($this->getUser());

            $this->em->persist($address);
            $this->em->flush();

            $this->addFlash('success', 'Votre adresse a bien été ajoutée');
            return $this->redirect($url);
        }

        return $this->render('security/user/address/add.html.twig', [
            'formAddAddress' => $form->createView(),
            'edit' => false,
            'description' => null
        ]);
    }

    #[Route('/edit-address/{id}', name: 'edit_address')]
    public function editAddress(Address $address, Request $request): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();

            $this->em->persist($address);
            $this->em->flush();

            $this->addFlash('success', 'Votre adresse a bien été modifié');
            return $this->redirectToRoute('user_my_account');
        }

        return $this->render('security/user/address/add.html.twig', [
            'formAddAddress' => $form->createView(),
            'edit' => $address->getId(),
            'description' => null
        ]);
    }

    #[Route('/delete-address/{id}', name: 'delete_address')]
    public function deleteAddress(Address $address) : Response
    {
        if ($address->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer cette adresse');
            return $this->redirectToRoute('user_my_account');
        }

        if (!$this->getUser())
        {
            $this->addFlash('danger', 'Vous devez être connecté pour supprimer une adresse');
            return $this->redirectToRoute('app_login');
        }

        $this->em->remove($address);
        $this->em->flush();

        $this->addFlash('success', 'Votre adresse a bien été supprimé');
        return $this->redirectToRoute('user_my_account');
    }

    #[Route('/delete-account/{id}', name: 'delete_account')]
    public function deleteUser(User $user): RedirectResponse
    {
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('success', 'Votre compte a bien été supprimé');
        return $this->redirectToRoute('app_home');
    }
}
