<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class UpdateAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
	        ->add('avatar', DropzoneType::class, [
		        'label' => 'Avatar',
		        'attr' => [
			        'placeholder' => 'Glisser un fichier ou cliquer pour le chercher',
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
