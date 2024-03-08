<?php

namespace App\Controller\Admin;

use App\Entity\Newsletter\Newsletter;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NewsletterCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Newsletter::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setEntityLabelInSingular("NewsletterUser")
      ->setEntityLabelInPlural("NewsletterUser")
      ->setSearchFields(["id", "name", "content", "is_sent", "created_at"])
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
    return $filters->add("is_sent")->add("created_at");
  }

  public function configureFields(string $pageName): iterable
  {
    yield NumberField::new("id")->hideOnForm();
    yield TextField::new("name");
    yield TextareaField::new("content");

    $checkSend = BooleanField::new("is_sent")
      ->hideOnForm()
      ->setFormTypeOption("disabled", true);

    $createdAt = DateTimeField::new("created_at")
      ->setFormat("dd-MM-yyyy")
      ->setFormTypeOptions([
        "data" => new DateTime("now", new DateTimeZone("Europe/Paris")), // default data
        "widget" => "single_text",
        "disabled" => true,
      ]);

    if (Crud::PAGE_EDIT === $pageName) {
      yield $createdAt->setFormTypeOption("disabled", true);
    } else {
      yield $createdAt;
      yield $checkSend;
    }
  }
}
