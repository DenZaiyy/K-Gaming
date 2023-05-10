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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\UX\Dropzone\Form\DropzoneType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
				'required' => true,
				'attr' => [
					'placeholder' => 'Pseudo',
					'class' => 'form-control'
				]
			])
            ->add('email', EmailType::class, [
				'required' => true,
				'attr' => [
					'placeholder' => 'Email',
					'class' => 'form-control'
				]
			])
            ->add('plainPassword', RepeatedType::class, [
				'mapped' => false,
				'type' => PasswordType::class,
				'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
				'options' => ['attr' => ['class' => 'password-field']],
				'required' => true,
				'first_options'  => ['label' => 'Mot de passe', 'attr' => ['placeholder' => 'Mot de passe', 'class' => 'form-control']],
				'second_options' => ['label' => 'Confirmer votre mot de passe', 'attr' => ['placeholder' => 'Confirmer votre mot de passe', 'class' => 'form-control']],
            ])
			->add('avatar', DropzoneType::class, [
				'label' => 'Avatar',
				'attr' => [
					'placeholder' => 'Glisser/déposer un fichier ou cliquer pour le chercher',
				],
				'mapped' => false,
				'required' => false,
				'constraints' => [
					new File([
						'maxSize' => "1024k",
						'mimeTypes' => [
							'image/jpg',
							'image/jpeg',
							'image/png'
						],
						'mimeTypesMessage' => "Veuillez upload une image valide",
						'extensions' => [
							'jpg',
							'jpeg',
							'png'
						],
						'extensionsMessage' => 'Veuillez upload une image valide',
					])
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
			])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
