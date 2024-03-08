<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $user = $options["user"];

    $builder
      ->add("addresses", EntityType::class, [
        "class" => Address::class,
        "label" => false,
        "required" => true,
        "multiple" => false,
        "choices" => $user->getAddresses(),
        "expanded" => false,
        "attr" => [
          "class" => "gap-2 d-flex flex-column align-content-center",
        ],
      ])
      ->add("payment", ChoiceType::class, [
        "label" => false,
        "required" => true,
        "multiple" => false,
        "choices" => [
          "Carte bancaire" => "stripe",
          "PayPal" => "paypal",
        ],
        "expanded" => true,
        "attr" => [
          "class" => "gap-2 d-flex flex-column align-content-center",
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      "user" => [],
    ]);
  }
}
