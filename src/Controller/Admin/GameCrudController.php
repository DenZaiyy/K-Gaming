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
use Symfony\Component\Translation\TranslatableMessage;

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
		        fn (?Game $product, ?string $pageName) => $product ? $product->getLabel() : new TranslatableMessage('menu.game.singular', [], 'admin')
	        )
            ->setEntityLabelInPlural(new TranslatableMessage('game.index.title', [], 'admin'))
	        ->setHelp(Crud::PAGE_INDEX, new TranslatableMessage('game.index.description', [], 'admin'))
            ->setSearchFields(['id', 'label', 'price', 'is_promotion', 'slug', 'date_release'])
	        ->setEntityPermission('ROLE_ADMIN')
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
		yield FormField::addTab(new TranslatableMessage('game.detail.general_information', [], 'admin'));
            yield NumberField::new('id', new TranslatableMessage('game.table.id', [], 'admin'))->hideOnForm();
		    yield FormField::addColumn(4);
		    yield FormField::addPanel(new TranslatableMessage('game.detail.name_game', [], 'admin'));
		        yield TextField::new('label', new TranslatableMessage('game.table.label', [], 'admin'));
		    yield FormField::addColumn(4);
		    yield FormField::addPanel(new TranslatableMessage('game.detail.slug', [], 'admin'));
                yield SlugField::new('slug', new TranslatableMessage('game.table.slug', [], 'admin'))->setTargetFieldName('label');
		    yield FormField::addColumn(4);
		    yield FormField::addPanel(new TranslatableMessage('game.detail.status', [], 'admin'))->setHelp(new TranslatableMessage('game.detail.status_description', [], 'admin'));
			    yield BooleanField::new('is_sellable', new TranslatableMessage('game.table.is_sellable', [], 'admin'));
	    yield FormField::addTab(new TranslatableMessage('game.detail.price_information', [], 'admin'));
			yield FormField::addColumn(4);
			yield FormField::addPanel(new TranslatableMessage('game.detail.price', [], 'admin'));
		        yield MoneyField::new('price', new TranslatableMessage('game.table.price', [], 'admin'))->setCurrency('EUR')->setStoredAsCents(false);
				yield MoneyField::new('old_price', new TranslatableMessage('game.table.old_price', [], 'admin'))->setCurrency('EUR')->setDisabled(true);
		    yield FormField::addColumn(4);
		    yield FormField::addPanel(new TranslatableMessage('game.detail.promotion', [], 'admin'));
				yield BooleanField::new('is_promotion', new TranslatableMessage('game.table.is_promotion', [], 'admin'))->setHelp(new TranslatableMessage('game.detail.promotion_description', [], 'admin'));
				yield NumberField::new('promo_percent', new TranslatableMessage('game.table.promotion_percent', [], 'admin'))->setHelp(new TranslatableMessage('game.detail.promotion_percent_description', [], 'admin'));
	    yield FormField::addTab(new TranslatableMessage('game.detail.game_information', [], 'admin'));
		    yield FormField::addColumn(4);
		    yield FormField::addPanel()->setHelp(new TranslatableMessage('game.detail.game_platforms_description', [], 'admin'));
                yield AssociationField::new('plateforms', new TranslatableMessage('game.table.platforms', [], 'admin'));
		    yield FormField::addColumn(4);
		    yield FormField::addPanel()->setHelp(new TranslatableMessage('game.detail.game_genders_description', [], 'admin'));
                yield AssociationField::new('genres', new TranslatableMessage('game.table.genders', [], 'admin'));
		    yield FormField::addColumn(4);
		    yield FormField::addPanel(new TranslatableMessage('game.detail.date_release', [], 'admin'));
			yield DateTimeField::new('date_release', new TranslatableMessage('game.table.release_date', [], 'admin'))
		            ->setFormat('dd-MM-yyyy')
		            ->setFormTypeOptions([
		                'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
		                'widget' => 'single_text',
			            'disabled' => true,
		            ]);
    }
}
