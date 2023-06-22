<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\Purchase;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\User\UpdatePasswordType;
use App\Form\User\UpdateUsernameType;
use App\Service\FileUploader;
use App\Service\MultiAvatars;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
	/*
	 * Constructeur permettant d'instancier l'entityManager et l'utiliser dans les fonctions
	 */
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/profil', name: 'user_my_account')]
    public function index(Request $request, FileUploader $uploader, UserPasswordHasherInterface $hasher, MultiAvatars $multiAvatars): Response
    {
        $user = $this->getUser();
	    $currentUser = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

        $avatars = $multiAvatars->getFiveRandomlyAvatar();

		$usernameForm = $this->createForm(UpdateUsernameType::class, $user);
		$passwordForm = $this->createForm(UpdatePasswordType::class, $user);

		$usernameForm->handleRequest($request);
		if ($usernameForm->isSubmitted() && $usernameForm->isValid())
		{
			$check = $this->em->getRepository(User::class)->findOneBy(['username' => $usernameForm->getData()->getUsername()]);
			if($check)
			{
				$this->addFlash('danger', 'Ce nom d\'utilisateur est déjà utilisé');
				return $this->redirectToRoute('user_my_account');
			}

			$currentUser->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
			$currentUser->setUsername($usernameForm->get('username')->getData());

			$this->em->persist($currentUser);
			$this->em->flush();

			$this->addFlash('success', 'Votre nom d\'utilisateur a bien été modifié');
			return $this->redirectToRoute('user_my_account');
		}

		$passwordForm->handleRequest($request);
		if ($passwordForm->isSubmitted() && $passwordForm->isValid())
		{
            if ($hasher->isPasswordValid($currentUser, $passwordForm->get('currentPassword')->getData()))
            {
                $currentUser->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
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
	        'passwordForm' => $passwordForm->createView(),
        ]);
    }

    #[Route('/profil/update-avatar/{image}', name: 'user_update_avatar')]
    public function updateUserAvatar($image, Request $request, MultiAvatars $multiAvatars) : Response
    {
        $user = $this->getUser();
        $currentUser = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);

        if($currentUser)
        {
            $currentUser->setAvatar($multiAvatars->setAvatarUrl($image));
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
    #[Route('/profil/my-order', name: 'user_my_order')]
    public function myOrder(): Response
    {
        $purchases = $this->em->getRepository(Purchase::class)->findBy(
			['user' => $this->getUser()],
			['created_at' => 'DESC']
        );

        return $this->render('security/user/my-order.html.twig', [
            'purchases' => $purchases,
        ]);
    }

	/**
	 * Fonction permettant d'accéder aux préférences utilisateur, notamment la newsletter et le choix du theme
	 */
    #[Route('/profil/my-preference', name: 'user_my_preference')]
    public function myPreference(): Response
    {
		$user = $this->getUser();
		$newsletter = $this->em->getRepository(NewsletterUser::class)->findOneBy(['email' => $user->getEmail()]);
        return $this->render('security/user/my-preference.html.twig', [
			'newsletter' => $newsletter,
        ]);
    }

	/*
	 * Fonction permettant d'ajouter une nouvelle adresse de facturation à l'utilisateur connecté
	 */
    #[Route('/profil/add-address', name: 'user_add_address')]
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
        ]);
    }
}
