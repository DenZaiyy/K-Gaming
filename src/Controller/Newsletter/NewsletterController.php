<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\User;
use App\Form\Newsletter\NewsletterType;
use App\Repository\Newsletter\NewsletterRepository;
use App\Repository\Newsletter\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/newsletter', name: 'newsletter_')]
#[IsGranted('ROLE_ADMIN')]
class NewsletterController extends AbstractController
{

	public function __construct(private EntityManagerInterface $em)
	{
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
}
