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
use Symfony\Component\Translation\TranslatableMessage;

class GenreCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Genre::class;
    }

	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular(fn (?Genre $gender, ?string $pageName) => $gender ? $gender->getLabel() : new TranslatableMessage('menu.game.gender', [], 'admin'))
			->setEntityLabelInPlural(new TranslatableMessage('gender.index.title', [], 'admin'))
			->setHelp(Crud::PAGE_INDEX, new TranslatableMessage('gender.index.description', [], 'admin'))
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
        yield NumberField::new('id', new TranslatableMessage('gender.table.id', [], 'admin'))->hideOnForm();
        yield TextField::new('label',new TranslatableMessage('gender.table.label', [], 'admin'));
        yield SlugField::new('slug', new TranslatableMessage('gender.table.slug', [], 'admin'))->setTargetFieldName('label');
    }

}
