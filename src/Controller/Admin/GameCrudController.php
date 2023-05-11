<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular('Game')
			->setEntityLabelInPlural('Game')
			->setSearchFields(['id', 'label', 'price', 'date_release'])
			->setDefaultSort(['date_release' => 'DESC']);
	}

	public function configureFilters(Filters $filters): Filters
	{
		return $filters
			->add('label')
			->add('price')
			->add('date_release');
	}


	public function configureFields(string $pageName): iterable
	{
		yield TextField::new('label');
		yield NumberField::new('price');

		$dateRelease = DateTimeField::new('date_release')->setFormTypeOptions([
			'data' => new \DateTime('now', new DateTimeZone('Europe/Paris')), // default data
			'widget' => 'single_text',
		]);

		if (Crud::PAGE_EDIT === $pageName) {
			yield $dateRelease->setFormTypeOption('disabled', true);
		} else {
			yield $dateRelease;
		}


	}
}
