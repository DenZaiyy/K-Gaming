<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\UX\Dropzone\Form\DropzoneType;

class RegistrationFormType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('username', TextType::class, [
				'required' => true,
				'label' => 'Pseudo',
				'attr' => [
					'placeholder' => 'Pseudo',
					'class' => 'form-control'
				]
			])
			->add('email', EmailType::class, [
				'required' => true,
				'label' => 'Email',
				'attr' => [
					'placeholder' => 'Email',
					'class' => 'form-control'
				],
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez entrer un email valide.',
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez entrer un email.',
                    ])
                ]
			])
			->add('plainPassword', RepeatedType::class, [
				'mapped' => false,
				'type' => PasswordType::class,
				'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
				'options' => [
					'attr' => ['class' => 'password-field']
				],
				'required' => true,
				'first_options' => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Mot de passe', 'class' => 'form-control']],
				'second_options' => ['label' => 'Confirmer votre mot de passe', 'attr' => ['placeholder' => 'Confirmer votre mot de passe', 'class' => 'form-control']],
				'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*\d)(?=.*[!-/:-@[-`{-~À-ÿ§µ²°£])(?=.*[a-z])(?=.*[A-Z])(?=.*[A-Za-z]).{12,32}$/',
                        'match' => true,
                        'message' => 'Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule, 1 nombre, 1 caractère spéciale et doit faire au moins 12 caractères.',
                    ]),
				],
			])
			->add('agreeTerms', CheckboxType::class, [
				'mapped' => false,
				'label' => 'Accepter les conditions',
				'attr' => [
					'class' => 'form-check-input'
				],
				'constraints' => [
					new IsTrue([
						'message' => 'Vous devez accepter nos conditions générales.',
					]),
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
