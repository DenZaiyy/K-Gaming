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
		//        return parent::index();

		// Option 1. You can make your dashboard redirect to some common page of your backend
		//
		 $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
		 return $this->redirect($adminUrlGenerator->setController(GameCrudController::class)->generateUrl());

		// Option 2. You can make your dashboard redirect to different pages depending on the user
		//
		// if ('jane' === $this->getUser()->getUsername()) {
		//     return $this->redirect('...');
		// }

		// Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
		// (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
		//
		// return $this->render('some/path/my-dashboard.html.twig');
	}

	public function configureDashboard(): Dashboard
	{
		return Dashboard::new()
			->setTitle('K-Gaming');
	}

	public function configureMenuItems(): iterable
	{
		yield MenuItem::linkToCrud('Jeux', 'fa-solid fa-gamepad', Game::class);
        yield MenuItem::linkToCrud('Genre', 'fa-solid fa-icons', Genre::class);
		yield MenuItem::linkToCrud('Note', 'fa-solid fa-star', Rating::class);
        yield MenuItem::linkToCrud('Stock', 'fa-solid fa-shop', Stock::class);
        yield MenuItem::linkToCrud('Commande', 'fa-solid fa-laptop', Purchase::class);
        yield MenuItem::linkToCrud('Utilisateur', 'fa-solid fa-user-gear', User::class);
        yield MenuItem::linkToCrud('Utilisateur newsletter', 'fa-solid fa-users-gear', NewsletterUser::class);
        yield MenuItem::linkToCrud('Newsletter', 'fa-solid fa-envelope-circle-check', Newsletter::class);
        yield MenuItem::linkToRoute('Retour sur le site', 'fas fa-home', 'app_home');
	}
}
