<?php

namespace App\Controller\Admin;

use App\Entity\Promotion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PromotionCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Promotion::class;
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

  public function configureFields(string $pageName): iterable
  {
    return [
      TextField::new("coupon"),
      NumberField::new("percent"),
      DateTimeField::new("start_at"),
      DateTimeField::new("end_at"),
    ];
  }
}
