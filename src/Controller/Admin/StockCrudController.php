<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class StockCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Stock::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setEntityLabelInSingular("Game Stock")
      ->setEntityLabelInPlural("Game Stock")
      ->setSearchFields([
        "id",
        "game_id",
        "purchase_id",
        "plateform_id",
        "license_key",
        "date_availability",
        "is_available",
      ])
      ->setDefaultSort(["date_availability" => "DESC"])
      ->setPaginatorPageSize(10)
      ->setEntityPermission("ROLE_EDITOR")
      ->showEntityActionsInlined();
  }

  public function configureActions(Actions $actions): Actions
  {
    return $actions
      ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
        return $action->setIcon("fa fa-square-plus");
      })
      ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
        return $action->setIcon("fa fa-pen-to-square");
      })
      ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
        return $action->setIcon("fa fa-trash-can");
      });
  }

  public function configureFilters(Filters $filters): Filters
  {
    return $filters
      ->add(EntityFilter::new("game"))
      ->add(EntityFilter::new("purchase"))
      ->add(EntityFilter::new("plateform"))
      ->add("is_available")
      ->add("date_availability");
  }

  public function configureFields(string $pageName): iterable
  {
    yield AssociationField::new("game");
    yield AssociationField::new("purchase");
    yield AssociationField::new("plateform");
    yield TextField::new("license_key");
    yield BooleanField::new("is_available");

    $dateAvailability = DateTimeField::new(
      "date_availability"
    )->setFormTypeOptions([
      "data" => new DateTime("now", new DateTimeZone("Europe/Paris")), // default data
      "widget" => "single_text",
    ]);

    if (Crud::PAGE_EDIT === $pageName) {
      yield $dateAvailability->setFormTypeOption("disabled", true);
    } else {
      yield $dateAvailability;
    }
  }
}
