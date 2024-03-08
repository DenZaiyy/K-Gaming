<?php

namespace App\Form\Newsletter;

use App\Entity\Newsletter\Newsletter;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options): void
    {
        $builder->add("name", TextType::class)->add("content", CKEditorType::class)->add(
          "enregistrer", SubmitType::class, ["attr" => ["class" => "btn-primary-orange",],]
        );
    }

    public function configureOptions (OptionsResolver $resolver): void
    {
        $resolver->setDefaults(["data_class" => Newsletter::class,]);
    }
}
