<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTime;
use DateTimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
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
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('User')
            ->setSearchFields(['id', 'username', 'roles', 'email', 'avatar', 'is_verified', 'created_at']);
    }

    /*public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('label')
            ->add('price')
            ->add('date_release')
            ->add(EntityFilter::new('plateforms'))
            ->add(EntityFilter::new('genres'));
    }*/


    public function configureFields(string $pageName): iterable
    {
        yield NumberField::new('id')->hideOnForm();
        yield TextField::new('username');
        yield ChoiceField::class::new('roles')
            ->setChoices([
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                ]
            )
            ->allowMultipleChoices();
        yield TextField::new('email');
        yield ImageField::new('avatar')
            ->setBasePath('uploads/avatars')
            ->setUploadDir('public/uploads/avatars')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        $createdAt = DateTimeField::new('CreateAt')
            ->setFormat('dd-MM-yyyy')
            ->setFormTypeOptions([
                'data' => new DateTime('now', new DateTimeZone('Europe/Paris')), // default data
                'widget' => 'single_text',
            ]);

        if (Crud::PAGE_EDIT === $pageName) {
            yield $createdAt->setFormTypeOption('disabled', true);
        } else {
            yield $createdAt;
        }


    }
}
