<?php

namespace App\Controller\Newsletter;

use App\Entity\Newsletter\NewsletterUser;
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
class SubscriptionController extends AbstractController
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
			'form' => $form->createView(),
            'description' => "Inscrivez-vous à la newsletter de K-GAMING pour recevoir les dernières actualités et promotions !"
		]);
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
        // $user = id de l'enregistrement dans la bdd pour l'entité NewsletterUser
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
