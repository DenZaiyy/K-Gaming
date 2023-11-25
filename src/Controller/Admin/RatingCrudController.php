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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RatingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rating::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Rating')
			->setEntityLabelInPlural('Rating')
			->setSearchFields(['id', 'game_id', 'user_id', 'platform_id', 'note', 'message', 'created_at'])
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
			->add('note')
			->add('game')
			->add('platform')
			->add('created_at')
			;
	}

	public function configureFields(string $pageName): iterable
	{
		yield NumberField::new('id')->hideOnForm();
		yield TextField::new('message', 'Commentaire');
		yield NumberField::new('note', 'Note');
		yield AssociationField::new('user', 'Utilisateur');
		yield AssociationField::new('game', 'Jeux');
		yield AssociationField::new('platform', 'Plateforme');
		yield DateField::new('created_at', 'Date de création');
	}
}
