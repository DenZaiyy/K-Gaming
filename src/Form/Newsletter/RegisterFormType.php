<?php

namespace App\Form\Newsletter;

use App\Entity\Newsletter\NewsletterUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegisterFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('email', EmailType::class, [
				'label' => 'Email',
				'attr' => [
					'placeholder' => 'Email',
					'class' => 'form-control',
				],
			])
			->add('isRgpd', CheckboxType::class, [
				'label' => 'J\'accepte que mes données soient utilisées pour la newsletter',
				'attr' => [
					'class' => 'form-check-input',
				],
				'required' => true,
				'constraints' => [
					new isTrue([
						'message' => 'Vous devez accepter que vos données soient utilisées pour la newsletter',
					]),
				]
			])
			->add('submit', SubmitType::class, [
				'label' => 'S\'inscrire',
				'attr' => [
					'class' => 'btn btn-outline-primary p-3 w-100',
				],
			])
			;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => NewsletterUser::class,
		]);
	}
}
