<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
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
			->setSearchFields(['id', 'label', 'slug']);
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
