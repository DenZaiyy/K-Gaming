<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'placeholder' => 'Mot de passe actuel',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new UserPassword([
                        'message' => 'Le mot de passe actuel est incorrect.',
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
                'first_options' => ['label' => 'Nouveau mot de passe', 'attr' => ['placeholder' => 'Nouveau mot de passe', 'class' => 'form-control']],
                'second_options' => ['label' => 'Confirmer le nouveau mot de passe', 'attr' => ['placeholder' => 'Confirmer le nouveau mot de passe', 'class' => 'form-control']],
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
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier le mot de passe',
                'attr' => [
                    'class' => 'btn btn-primary p-2 w-100'
                ]
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
