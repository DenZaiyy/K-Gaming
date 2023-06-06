<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
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
            ->setSearchFields(['id', 'label', 'price', 'slug', 'date_release']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('label')
            ->add('price')
            ->add('date_release')
            ->add(EntityFilter::new('plateforms'))
            ->add(EntityFilter::new('genres'));
    }


    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('id')->hideOnForm();
        yield TextField::new('label');
        yield SlugField::new('slug')->setTargetFieldName('label');
        yield MoneyField::new('price')->setCurrency('EUR');
        yield AssociationField::new('plateforms');
        yield AssociationField::new('genres');

        $dateRelease = DateTimeField::new('date_release')
            ->setFormat('dd-MM-yyyy')
            ->setFormTypeOptions([
                'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
                'widget' => 'single_text',
            ]);

        if (Crud::PAGE_EDIT === $pageName) {
            yield $dateRelease->setFormTypeOption('disabled', true);
        } else {
            yield $dateRelease;
        }


    }
}
