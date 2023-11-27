<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Newsletter\Newsletter;
use App\Entity\Newsletter\NewsletterUser;
use App\Entity\Purchase;
use App\Entity\Rating;
use App\Entity\Stock;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
	#[Route('/admin', name: 'app_admin')]
	public function index(): Response
	{
		 $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
		 return $this->redirect($adminUrlGenerator->setController(GameCrudController::class)->generateUrl());
	}

	public function configureDashboard(): Dashboard
	{
		return Dashboard::new()
			->setTitle('K-Gaming')
			->renderContentMaximized()
			->setLocales(['fr', 'en']);
	}

	public function configureMenuItems(): iterable
	{
		return [
			MenuItem::linkToRoute('Retour sur le site', 'fas fa-home', 'app_home'),

			MenuItem::section('Jeux'),
			MenuItem::linkToCrud('Jeux', 'fa-solid fa-gamepad', Game::class),
			MenuItem::linkToCrud('Genre', 'fa-solid fa-icons', Genre::class),
			MenuItem::linkToCrud('Note', 'fa-solid fa-star', Rating::class),

			MenuItem::section('Boutique'),
			MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-user-gear', User::class),
			MenuItem::linkToCrud('Commandes', 'fa-solid fa-laptop', Purchase::class),
			MenuItem::linkToCrud('Stocks', 'fa-solid fa-shop', Stock::class),

			MenuItem::section('Marketing'),
			MenuItem::linkToCrud('Newsletter', 'fa-solid fa-paper-plane', Newsletter::class),
			MenuItem::linkToCrud('Utilisateurs inscrits', 'fa-solid fa-users-gear', NewsletterUser::class),

			MenuItem::section('Paramètres')->setPermission('ROLE_ADMIN'),
			MenuItem::linkToUrl('Maintenance', 'fa-solid fa-shield-halved', '')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),
			MenuItem::linkToRoute('Gestion du site', 'fa-solid fa-user-tie', '')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),
//			MenuItem::linkToRoute('Mode de paiements', 'fa-solid fa-money-check', '')->setPermission('ROLE_ADMIN')->setBadge('Not up', 'danger'),
			MenuItem::linkToRoute('Administrateurs', 'fa-solid fa-user-tie', '')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),
		];
	}
}
