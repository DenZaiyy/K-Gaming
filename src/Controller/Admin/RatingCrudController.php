<?php

namespace App\Controller\Admin;

use App\Entity\Rating;
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
			->setSearchFields(['id', 'game_id', 'user_id', 'platform_id', 'note', 'message', 'created_at']);
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
