<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class UserCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return User::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setEntityLabelInSingular(
        fn(?User $user, ?string $pageName) => $user
          ? $user->getUsername()
          : "User"
      )
      ->setEntityLabelInPlural(
        new TranslatableMessage("user.index.title", [], "admin")
      )
      ->setSearchFields([
        "id",
        "username",
        "roles",
        "email",
        "avatar",
        "is_verified",
        "is_banned",
        "create_at",
      ])
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
      ->add("roles")
      ->add("isVerified")
      ->add("isBanned")
      ->add("createAt");
  }

  public function configureFields(string $pageName): iterable
  {
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("user.detail.user_informations", [], "admin")
    );
    yield NumberField::new(
      "id",
      new TranslatableMessage("user.table.id", [], "admin")
    )->hideOnForm();
    yield TextField::new(
      "username",
      new TranslatableMessage("user.table.username", [], "admin")
    )->setSortable(false);
    yield TextField::new(
      "email",
      new TranslatableMessage("user.table.email", [], "admin")
    )->setSortable(false);
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("user.detail.roles", [], "admin")
    )->setHelp(
      new TranslatableMessage("user.table.roles.description", [], "admin")
    );
    yield ChoiceField::new(
      "roles",
      new TranslatableMessage("user.table.roles.index", [], "admin")
    )
      ->setTranslatableChoices([
        "ROLE_USER" => new TranslatableMessage(
          "user.table.roles.ROLE_USER",
          [],
          "admin"
        ),
        "ROLE_ADMIN" => new TranslatableMessage(
          "user.table.roles.ROLE_ADMIN",
          [],
          "admin"
        ),
        "ROLE_EDITOR" => new TranslatableMessage(
          "user.table.roles.ROLE_EDITOR",
          [],
          "admin"
        ),
      ])
      ->renderExpanded()
      ->allowMultipleChoices();

    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("user.detail.status", [], "admin")
    )->setHelp(
      new TranslatableMessage("user.table.is_banned_description", [], "admin")
    );
    yield BooleanField::new(
      "is_verified",
      new TranslatableMessage("user.table.is_verified", [], "admin")
    )
      ->hideOnForm()
      ->setDisabled(true);
    yield BooleanField::new(
      "is_banned",
      new TranslatableMessage("user.table.is_banned", [], "admin")
    );
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("user.detail.dates", [], "admin")
    );
    yield DateTimeField::new(
      "create_at",
      new TranslatableMessage("user.table.created_at", [], "admin")
    )
      ->setFormat("dd-MM-yyyy")
      ->setFormTypeOptions([
        "data" => new \DateTimeImmutable(
          "now",
          new DateTimeZone("Europe/Paris")
        ), // default data
        "widget" => "single_text",
        "disabled" => true,
      ]);

    yield DateTimeField::new(
      "updated_at",
      new TranslatableMessage("user.table.updated_at", [], "admin")
    )
      ->setFormat("dd-MM-yyyy")
      ->setFormTypeOptions([
        "widget" => "single_text",
        "disabled" => true,
      ]);
  }
}
