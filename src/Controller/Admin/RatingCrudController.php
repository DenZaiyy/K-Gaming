<?php

namespace App\Controller\Admin;

use App\Entity\Rating;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class RatingCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Rating::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setEntityLabelInSingular(
        new TranslatableMessage("rating.detail.rating", [], "admin")
      )
      ->setEntityLabelInPlural(
        new TranslatableMessage("rating.index.title", [], "admin")
      )
      ->setHelp(
        Crud::PAGE_INDEX,
        new TranslatableMessage("rating.index.description", [], "admin")
      )
      ->setSearchFields([
        "id",
        "game_id",
        "user_id",
        "platform_id",
        "note",
        "message",
        "created_at",
      ])
      ->setDefaultSort(["created_at" => "DESC", "updated_at" => "DESC"])
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
      ->add("note")
      ->add("user")
      ->add("game")
      ->add("platform")
      ->add("created_at");
  }

  public function configureFields(string $pageName): iterable
  {
    yield NumberField::new(
      "id",
      new TranslatableMessage("rating.table.id", [], "admin")
    )->hideOnForm();
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("rating.detail.comment_and_rating", [], "admin")
    );
    yield TextareaField::new(
      "message",
      new TranslatableMessage("rating.detail.comment", [], "admin")
    );
    yield NumberField::new(
      "note",
      new TranslatableMessage("rating.detail.rating", [], "admin")
    )->setHelp(
      new TranslatableMessage("rating.detail.rating_description", [], "admin")
    );
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage(
        "rating.detail.complementary_informations",
        [],
        "admin"
      )
    );
    yield AssociationField::new(
      "user",
      new TranslatableMessage("rating.detail.created_by", [], "admin")
    )
      ->setHelp(
        new TranslatableMessage(
          "rating.detail.created_by_description",
          [],
          "admin"
        )
      )
      ->setDisabled(true);
    yield AssociationField::new(
      "game",
      new TranslatableMessage("rating.detail.game", [], "admin")
    )
      ->setHelp(
        new TranslatableMessage("rating.detail.game_description", [], "admin")
      )
      ->setDisabled(true);
    yield AssociationField::new(
      "platform",
      new TranslatableMessage("rating.detail.platform", [], "admin")
    )
      ->setHelp(
        new TranslatableMessage(
          "rating.detail.platform_description",
          [],
          "admin"
        )
      )
      ->setDisabled(true);
    yield FormField::addColumn(4);
    yield FormField::addPanel(
      new TranslatableMessage("rating.detail.dates", [], "admin")
    )->setHelp(
      new TranslatableMessage("rating.detail.dates_description", [], "admin")
    );
    yield DateField::new(
      "created_at",
      new TranslatableMessage("rating.detail.created_at", [], "admin")
    )->setDisabled(true);
    yield DateField::new(
      "updated_at",
      new TranslatableMessage("rating.detail.updated_at", [], "admin")
    )->setDisabled(true);
  }
}
