<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PurchaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn (): string
    {
        return Purchase::class;
    }

    public function configureCrud (Crud $crud): Crud
    {
        return $crud->setEntityLabelInSingular(
          fn(?Purchase $purchase, ?string $pageName) => $purchase ? $purchase->getReference() : "Purchase"
        )->setEntityLabelInPlural("Purchases")->setSearchFields(["id",
          "delivery",
          "user_full_name",
          "is_paid",
          "method",
          "reference",
          "created_at",])->setEntityPermission("ROLE_EDITOR")->showEntityActionsInlined();
    }

    public function configureActions (Actions $actions): Actions
    {
        return $actions->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon("fa fa-square-plus");
        })->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action->setIcon("fa fa-pen-to-square");
        })->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action->setIcon("fa fa-trash-can");
        });
    }

    public function configureFilters (Filters $filters): Filters
    {
        return $filters->add("reference")->add("userFullName")->add("method")->add("isPaid");
    }

    public function configureFields (string $pageName): iterable
    {
        $createdAt = DateTimeField::new("created_at")->setFormat("dd-MM-yyyy")->setFormTypeOptions(
          ["data" => new DateTime("now", new DateTimeZone("Europe/Paris")),
              // default data
            "widget" => "single_text",]
        );

        yield FormField::addColumn(2);
        yield FormField::addPanel("Informations");
        yield NumberField::new("id")->hideOnForm();
        yield TextField::new("reference")->setSortable(false);
        if (Crud::PAGE_EDIT === $pageName) {
            yield DateField::new("created_at")->setDisabled(true)->setFormat("dd-MM-yyyy")->setFormTypeOptions(
              ["data" => new DateTime("now", new DateTimeZone("Europe/Paris")),
                  // default data
                "widget" => "single_text",]
            );
        }
        yield FormField::addColumn(2);
        yield FormField::addPanel("Informations client");
        yield TextField::new("user_full_name")->setDisabled(true);
        yield TextField::new("delivery")->setDisabled(true)->setSortable(false);
        yield FormField::addColumn(2);
        yield FormField::addPanel("Méthode de paiement");
        yield TextField::new("method")->setDisabled(true);
        yield BooleanField::new("is_paid")->setDisabled(true);
        yield FormField::addColumn(4);
        yield FormField::addPanel("Pseudo de l'utilisateur");
        Crud::PAGE_EDIT === $pageName
          ? yield TextField::new("user")->setDisabled(true)
          : yield AssociationField::new(
          "user"
        )->hideOnForm();
        yield FormField::addColumn(4);
        yield FormField::addPanel("Récapitulatif de la commande");
        Crud::PAGE_EDIT === $pageName ? yield ArrayField::new("recapDetails")->setDisabled(true)
          : yield AssociationField::new("recapDetails", "Produit achetés")->setDisabled(true)->setSortable(false);
        yield FormField::addColumn(4);
        yield FormField::addPanel("Facture");
        yield TextField::new("facture")->setDisabled(true)->setSortable(false);
        if (Crud::PAGE_EDIT === $pageName) {
            yield DateField::new("facture.created_at")->setDisabled(true)->setFormat("dd-MM-yyyy")->setFormTypeOptions(
              ["data" => new DateTime("now", new DateTimeZone("Europe/Paris")),
                  // default data
                "widget" => "single_text",]
            );
        }
        yield $createdAt->hideOnForm();

        if (Crud::PAGE_EDIT === $pageName) {
            yield $createdAt->setFormTypeOption("disabled", true);
        } else {
            yield $createdAt;
        }
    }
}
