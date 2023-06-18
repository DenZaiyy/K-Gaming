<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\User as AppUser;
use App\Form\Newsletter\NewsletterType;
use App\Form\Newsletter\RegisterFormType;
use App\Repository\Newsletter\NewsletterRepository;
use App\Repository\Newsletter\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletter', name: 'newsletter_')]
class NewsletterController extends AbstractController
{

	public function __construct(private EntityManagerInterface $em)
	{
	}

	/**
	 * Fonction permettant de s'inscrire à la newsletter
	 */
	#[Route('/subscribe', name: 'subscribe')]
	public function index(MailerInterface $mailer, Request $request): Response
	{
		$newsletter = new NewsletterUser(); //create a new newsletter user
		$token = hash('md5', uniqid()); //generate a token with a unique id and hash it with md5

		$form = $this->createForm(RegisterFormType::class, $newsletter);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$emailAddress = $form->get('email')->getData(); //get the email address

			$check = $this->em->getRepository(NewsletterUser::class)->findOneBy(['email' => $emailAddress]); //check if the user is already registered

			if ($check) {
				$this->addFlash('danger', 'Vous êtes déjà inscrit à la newsletter');
				return $this->redirectToRoute('app_home');
			}

			$newsletter->setEmail($emailAddress);
			$newsletter->setIsVerified(false);
			$newsletter->setIsRgpd(true);
			$newsletter->setToken($token);

			$this->em->persist($newsletter);
			$this->em->flush();

			//send the email to the user
			$email = (new TemplatedEmail())
				->from(new Address('support@k-grischko.fr', 'K-GAMING - Newsletter')) //set the sender
				->to($form->get('email')->getData()) //get the user email
				->subject('Confirmer l\'inscription à la newsletter') //set the subject
				->htmlTemplate('newsletter/emails/confirmation.html.twig') //set the template
				->context(compact('emailAddress', 'newsletter', 'token'));

			$mailer->send($email); //send the email

			$this->addFlash('success', "Un email de confirmation vous a été envoyé à l'adresse suivante : " . $emailAddress);
			return $this->redirectToRoute('app_home');
		}

		return $this->render('newsletter/subscribe.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * La fonction add permet d'ajouter une newsletter dans la base de données
	 */
	#[Route('/add', name: 'add')]
	public function prepare(Request $request): Response
	{
		$newsletter = new Newsletter();
		$form = $this->createForm(NewsletterType::class, $newsletter);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->persist($newsletter);
			$this->em->flush();

			$this->addFlash('success', 'La newsletter a bien été créée');
			return $this->redirectToRoute('newsletter_list');
		}

		return $this->render('newsletter/form.html.twig', [
			'form' => $form->createView(),
			'edit' => false
		]);
	}

	/**
	 * La fonction edit permet de modifier une newsletter dans la base de données
	 */
	#[Route('/edit/{id}', name: 'edit')]
	public function edit(Request $request, Newsletter $newsletter): Response
	{
		$form = $this->createForm(NewsletterType::class, $newsletter);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->em->flush();

			$this->addFlash('success', 'La newsletter a bien été modifiée');
			return $this->redirectToRoute('newsletter_list');
		}

		return $this->render('newsletter/form.html.twig', [
			'form' => $form->createView(),
			'edit' => $newsletter->getId()
		]);
	}

	/**
	 * La fonction delete permet de supprimer une newsletter dans la base de données
	 */
	#[Route('/delete/{id}', name: 'delete')]
	public function delete(Newsletter $newsletter): Response
	{
		$this->em->remove($newsletter);
		$this->em->flush();

		$this->addFlash('success', 'La newsletter a bien été supprimée');
		return $this->redirectToRoute('newsletter_list');
	}

	/**
	 * La fonction list permet d'afficher la liste des newsletters
	 */
	#[Route('/list', name: 'list')]
	public function list(NewsletterRepository $repository, UserRepository $userRepository): Response
	{
		$newsletters = $repository->findAll();
		$users = $userRepository->findAll();

		return $this->render('newsletter/list.html.twig', [
			'newsletters' => $newsletters,
			'users' => $users
		]);
	}

	//	FINISH THIS
	#[Route('/send/{id}', name: 'send')]
	public function send(Newsletter $newsletter, MailerInterface $mailer): Response
	{
		$users = $this->em->getRepository(NewsletterUser::class)->findBy(['is_verified' => true]);

		foreach ($users as $user) {
			if ($user->isIsVerified()) {
				$email = (new TemplatedEmail())
					->from(new Address('newsletter@k-grischko.fr', 'K-Gaming - Newsletter'))
					->to($user->getEmail())
					->subject($newsletter->getName())
					->htmlTemplate('newsletter/emails/send.html.twig')
					->context(compact('newsletter', 'user'))
				;

				$mailer->send($email);
			}
		}

		$newsletter->setIsSent(true);
		$this->em->persist($newsletter);
		$this->em->flush();

		$this->addFlash('success', 'La newsletter a bien été envoyée');

		return $this->redirectToRoute('newsletter_list');
	}

	/**
	 * La fonction confirm permet de confirmer l'inscription à la newsletter
	 */
	#[Route('/confirm/{id}/{token}', name: 'confirm')]
	public function confirm(NewsletterUser $newsletter, $token): Response
	{

		if ($newsletter->getToken() != $token) {
			throw $this->createNotFoundException('Le token n\'est pas valide');
		}

		if ($newsletter->isIsVerified()) {
			$this->addFlash('danger', 'Votre inscription à la newsletter a déjà été confirmée');
			return $this->redirectToRoute('app_home');
		}

		$newsletter->setIsVerified(true);
		$this->em->persist($newsletter);
		$this->em->flush();

		$this->addFlash('success', 'Votre inscription à la newsletter a bien été confirmée');
		return $this->redirectToRoute('app_home');
	}

	/**
	 * La fonction unsubscribe permet de se désinscrire de la newsletter
	 */
	#[Route('/unsubscribe/{id}/{token}', name: 'unsubscribe')]
	public function unsubscribe(NewsletterUser $user, $token): Response
	{
		if($user->getToken() !== $token) {
			$this->addFlash('danger', 'Le token n\'est pas valide');
			return $this->redirectToRoute('app_home');
		}

		$this->em->remove($user);
		$this->em->flush();

		$this->addFlash('success', 'Vous avez bien été désinscrit de la newsletter');
		return $this->redirectToRoute('app_home');
	}
}
