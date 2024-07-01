<?php

namespace App\Form\Newsletter;

use App\Entity\Newsletter\NewsletterUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
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
				'label' => new TranslatableMessage('home.newsletter.rgpd.check_msg', [], 'messages'),
				'attr' => [
					'class' => 'form-check-input',
					'title' => new TranslatableMessage('home.newsletter.rgpd.check_msg', [], 'messages'),
				],
				'required' => true,
				'constraints' => [
					new isTrue([
						'message' => new TranslatableMessage('home.newsletter.rgpd.constraint', [], 'messages'),
					]),
				]
			])
			->add('submit', SubmitType::class, [
				'label' => new TranslatableMessage('home.newsletter.submit', [], 'messages'),
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
