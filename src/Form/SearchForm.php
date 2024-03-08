<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add("q", TextType::class, [
        "label" => false,
        "required" => false,
        "attr" => [
          "placeholder" => "Rechercher",
        ],
      ])
      ->add("genres", EntityType::class, [
        "label" => false,
        "required" => false,
        "class" => Genre::class,
        "expanded" => true,
        "multiple" => true,
      ])
      ->add("min", NumberType::class, [
        "label" => false,
        "required" => false,
        "attr" => [
          "placeholder" => "Prix min",
        ],
      ])
      ->add("max", NumberType::class, [
        "label" => false,
        "required" => false,
        "attr" => [
          "placeholder" => "Prix max",
        ],
      ])
      ->add("preorder", CheckboxType::class, [
        "label" => "En précommande",
        "required" => false,
      ])
      ->add("promotion", CheckboxType::class, [
        "label" => "En promotion",
        "required" => false,
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      "data_class" => SearchData::class,
      "method" => "GET",
      "csrf_protection" => false,
    ]);
  }

  public function getBlockPrefix(): string
  {
    return "";
  }
}
