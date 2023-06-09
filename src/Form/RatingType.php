<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
	            'label' => 'Message',
	            'required' => false,
            ])
	        ->add('note', HiddenType::class, [
	            'label' => 'Note',
		        'attr' => [
					'value' => 0,
			        'min' => 1,
			        'max' => 5,
		        ],
	            'required' => true,
	        ])
	        ->add('submit', SubmitType::class, [
	            'label' => 'Envoyer',
	        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
	        'user' => null,
        ]);
    }
}
