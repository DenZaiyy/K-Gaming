<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\NewsletterUser;
use App\Entity\User as AppUser;
use App\Form\Newsletter\RegisterFormType;
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

	#[Route('/subscribe', name: 'subscribe')]
	public function index(MailerInterface $mailer, Request $request): Response
	{
		$user = $this->getUser();
		$newsletter = new NewsletterUser();
		$token = hash('md5', uniqid());

		$form = $this->createForm(RegisterFormType::class, $newsletter);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$emailAddress = $form->get('email')->getData();

			$check = $this->em->getRepository(NewsletterUser::class)->findOneBy(['email' => $emailAddress]);

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
				->from(new Address('support@k-grischko.fr', 'K-GAMING - Confirmer l\'inscription newsletter')) //set the sender
				->to($form->get('email')->getData()) //get the user email
				->subject('Confirmer l\'inscription à la newsletter') //set the subject
				->htmlTemplate('newsletter/confirmation.html.twig') //set the template
				->context(compact('emailAddress', 'newsletter', 'token'));

			$mailer->send($email); //send the email

			$this->addFlash('success', "Un email de confirmation vous a été envoyé à l'adresse suivante : " . $emailAddress);
			return $this->redirectToRoute('app_home');
		}

		return $this->render('newsletter/form.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route('/unsubscribe', name: 'unsubscribe')]
	public function unsubscribe() : Response
	{
		$user = $this->getUser();

		if (!$user) {
			$this->addFlash('danger', 'Vous devez être connecté pour vous désinscrire de la newsletter');
			return $this->redirectToRoute('app_login');
		}

		$newsletter = $this->em->getRepository(NewsletterUser::class)->findOneBy(['email' => $user->getEmail()]);

		if (!$newsletter) {
			$this->addFlash('danger', 'Vous n\'êtes pas inscrit à la newsletter');
			return $this->redirectToRoute('app_home');
		}

		$this->em->remove($newsletter);
		$this->em->flush();

		$this->addFlash('success', 'Vous avez bien été désinscrit de la newsletter');
		return $this->redirectToRoute('app_home');
	}

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
}
