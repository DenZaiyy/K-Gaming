<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
	public static function getEntityFqcn(): string
	{
		return User::class;
	}
	
	public function configureCrud(Crud $crud): Crud
	{
		return $crud
			->setEntityLabelInSingular(fn (?User $user, ?string $pageName) => $user ? $user->getUsername() : 'User')
			->setEntityLabelInPlural('Users')
			->setSearchFields(['id', 'username', 'roles', 'email', 'avatar', 'is_verified', 'is_banned', 'create_at'])
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
			->add('roles')
			->add('isVerified')
			->add('isBanned')
			->add('createAt');
	}
	
	public function configureFields(string $pageName): iterable
	{
		yield NumberField::new('id')->hideOnForm();
		yield TextField::new('username')->setSortable(false);
		yield ChoiceField::class::new('roles')
			->setChoices([
					'Membre' => 'ROLE_USER',
					'Administrateur' => 'ROLE_ADMIN',
				]
			)
			->allowMultipleChoices();
		yield TextField::new('email')->setSortable(false);
		
		$verifiedAcc = BooleanField::new('is_verified')
			->hideOnForm()
			->setFormTypeOption('disabled', true);
		
		$isBanned = BooleanField::new('is_banned')->hideOnForm();
		
		$createdAt = DateField::new('create_at')
			->setFormat('dd-MM-yyyy')
			->setFormTypeOptions([
				'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
				'widget' => 'single_text',
			]);
		
		if (Crud::PAGE_EDIT === $pageName) {
			yield $createdAt->setFormTypeOption('disabled', true);
			yield $isBanned->setFormTypeOption('disabled', true);
		} else {
			yield $createdAt;
			yield $verifiedAcc;
			yield $isBanned;
		}
	}
}
