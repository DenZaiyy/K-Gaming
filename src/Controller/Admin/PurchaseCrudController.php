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
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PurchaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Purchase::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Purchase')
            ->setEntityLabelInPlural('Purchase')
            ->setSearchFields(['id', 'delivery', 'user_full_name', 'is_paid', 'method', 'reference', 'created_at'])
	        ->setEntityPermission('ROLE_EDITOR')
	        ->showEntityActionsInlined();
    }

	public function configureActions(Actions $actions): Actions
	{
		return $actions
			->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
				return $action->setIcon('fa fa-square-plus');
			})
			->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
				return $action->setIcon('fa fa-pen-to-square');
			})
			->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
				return $action->setIcon('fa fa-trash-can');
			})
			;
	}

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('userFullName')
            ->add('method')
            ->add('isPaid')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('id')->hideOnForm();
        yield TextField::new('reference');
        yield TextField::new('user_full_name');
        yield TextField::new('delivery');
        yield TextField::new('method');
        yield AssociationField::new('user')->hideOnForm();
        yield AssociationField::new('stock')->hideOnForm();

        $createdAt = DateTimeField::new('created_at')
            ->setFormat('dd-MM-yyyy')
            ->setFormTypeOptions([
                'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
                'widget' => 'single_text',
            ]);

        if (Crud::PAGE_EDIT === $pageName) {
            yield $createdAt->setFormTypeOption('disabled', true);
        } else {
            yield $createdAt;
        }
    }
}
