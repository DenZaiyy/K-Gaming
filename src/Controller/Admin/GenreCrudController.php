<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class GenreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Genre::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Genre')
			->setEntityLabelInPlural('Genre')
			->setSearchFields(['id', 'label', 'slug'])
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
			->add('label');
	}

    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('id')->hideOnForm();
        yield TextField::new('label');
        yield SlugField::new('slug')->setTargetFieldName('label');
    }

}
