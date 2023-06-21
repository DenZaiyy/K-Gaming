<?php

namespace App\Controller\User;

use App\Entity\Address;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\Purchase;
use App\Entity\User;
use App\Form\AddressType;
use App\Form\User\UpdateAvatarType;
use App\Form\User\UpdatePasswordType;
use App\Form\User\UpdateUsernameType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Imagick;
use Multiavatar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function index(Request $request, FileUploader $uploader): Response
    {
        $user = $this->getUser();
	    $currentUser = $this->em->getRepository(User::class)->findOneBy(['username' => $user->getUsername()]);
	    $currentAvatarPath = $user->getAvatar();

	    $multiavatar = new Multiavatar();

		for ($i = 0; $i < 5; $i++)
		{
			$avatars[] = $multiavatar(strval(rand(1, 1000)), false, null);
		}

		$avatarForm = $this->createForm(UpdateAvatarType::class, $user);
		$usernameForm = $this->createForm(UpdateUsernameType::class, $user);
		$passwordForm = $this->createForm(UpdatePasswordType::class, $user);

		$avatarForm->handleRequest($request);
		if ($avatarForm->isSubmitted() && $avatarForm->isValid())
		{
			$avatar = $avatarForm->get('avatar')->getData();
			$newAvatar = $uploader->upload($avatar);

			$currentUser->setAvatar($newAvatar);
			$currentUser->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));

			$this->em->persist($currentUser);
			$this->em->flush();

			$this->addFlash('success', 'Votre avatar a bien été modifié');
			return $this->redirectToRoute('user_my_account');
		}

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

		}

        return $this->render('security/user/index.html.twig', [
            'user' => $user,
			'avatars' => $avatars,
	        'avatarForm' => $avatarForm->createView(),
	        'usernameForm' => $usernameForm->createView(),
	        'passwordForm' => $passwordForm->createView(),
	        'currentAvatarPath' => $currentAvatarPath,
        ]);
    }

    public function convertSVGtoPNG($svgCode, $outputFile): void
    {
        // Create a new Imagick object
        $imagick = new Imagick();

        // Set the format to PNG
        $imagick->setFormat('png');

        // Read the SVG code
        $imagick->readImageBlob($svgCode);

        // Save the image to the output file
        $imagick->writeImage($outputFile);

        // Destroy the Imagick object
        $imagick->destroy();
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
