<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateUsernameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
				'label' => 'Nom d\'utilisateur',
	            'attr' => [
	            	'placeholder' => 'Nom d\'utilisateur',
		            'class' => 'form-control'
	            ]
            ])
	        ->add('submit', SubmitType::class, [
		        'label' => 'Modifier',
		        'attr' => [
			        'class' => 'btn btn-primary mt-3'
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
