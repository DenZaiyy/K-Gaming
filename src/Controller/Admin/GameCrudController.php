<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
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

	public function updateEntity (EntityManagerInterface $entityManager, $entityInstance): void
	{

		$uow = $entityManager->getUnitOfWork();
		$originalEntityData = $uow->getOriginalEntityData($entityInstance);

		// Check if isPromotion or promoPercent fields are changed
		if ($originalEntityData['is_promotion'] === $entityInstance->isIsPromotion() && $originalEntityData['promo_percent'] === $entityInstance->getPromoPercent())
		{
			parent::updateEntity($entityManager, $entityInstance);
			return;
		}

		if ($entityInstance->isIsPromotion())
		{
			$entityInstance->setOldPrice($entityInstance->getPrice());
			$entityInstance->setPrice($entityInstance->getPrice() - ($entityInstance->getPrice() * $entityInstance->getPromoPercent() / 100));
		} else {
			if ($entityInstance->getOldPrice() !== null)
			{
				$entityInstance->setPrice($entityInstance->getOldPrice());
			} else {
				$entityInstance->setPrice($entityInstance->getPrice());
			}

			$entityInstance->setOldPrice(null);
			$entityInstance->setPromoPercent(null);
		}

		parent::updateEntity($entityManager, $entityInstance);
	}

	public function configureCrud(Crud $crud): Crud
    {
        return $crud
	        ->setEntityLabelInSingular(
		        fn (?Game $product, ?string $pageName) => $product ? $product->getLabel() : 'Jeux'
	        )
            ->setEntityLabelInPlural('Liste de jeux')
            ->setSearchFields(['id', 'label', 'price', 'is_promotion', 'slug', 'date_release'])
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
            ->add('label')
            ->add('price')
	        ->add('is_promotion')
	        ->add('is_sellable')
            ->add('date_release')
            ->add(EntityFilter::new('plateforms'))
            ->add(EntityFilter::new('genres'));
    }


    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('id')->hideOnForm();

		yield FormField::addTab('Informations générales');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Nom du jeux');
		        yield TextField::new('label');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Slug');
                yield SlugField::new('slug')->setTargetFieldName('label');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Statut');
			    yield BooleanField::new('is_sellable');
	    yield FormField::addTab('Informations sur le prix');
			yield FormField::addColumn(4);
			yield FormField::addPanel('Prix');
		        yield MoneyField::new('price')->setCurrency('EUR');
				yield MoneyField::new('old_price')->setCurrency('EUR')->setDisabled(true);
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Promotion');
				yield BooleanField::new('is_promotion')->setDisabled(true);
				yield NumberField::new('promo_percent')->setDisabled(true);
	    yield FormField::addTab('Informations sur le jeu');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Plateformes associées');
                yield AssociationField::new('plateforms');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Genres associés');
                yield AssociationField::new('genres');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel('Date de sortie');
		        $dateRelease = DateTimeField::new('date_release')
		            ->setFormat('dd-MM-yyyy')
		            ->setFormTypeOptions([
		                'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
		                'widget' => 'single_text',
		            ]);

        if (Crud::PAGE_EDIT === $pageName) {
            yield $dateRelease->setFormTypeOption('disabled', false);
	        yield BooleanField::new('is_promotion')->setDisabled(false);
	        yield NumberField::new('promo_percent')->setDisabled(false);

        } else {
            yield $dateRelease;
        }

    }
}
