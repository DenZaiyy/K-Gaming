<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Unique;

class UpdateEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
				'label' => 'Adresse email',
	            'attr' => [
	            	'placeholder' => 'Adresse email',
		            'class' => 'form-control'
	            ],
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez entrer un email valide.',
                    ]),
                    new Unique([
                        'message' => 'Cet email est déjà utilisé.',
                    ])
                ]
            ])
	        ->add('submit', SubmitType::class, [
		        'label' => 'Modifier',
		        'attr' => [
			        'class' => 'btn btn-primary'
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
