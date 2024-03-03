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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN', message: "No access", statusCode: 403)]
class DashboardController extends AbstractDashboardController
{
	#[Route('/{_locale<%app.supported_locales%>}/admin/', name: 'app_admin')]
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
			->setTranslationDomain('admin')
			->setLocales(['fr', 'en']);
	}

	public function configureMenuItems(): iterable
	{
		return [
			MenuItem::linkToRoute(new TranslatableMessage('menu.back_home', [], 'admin'), 'fas fa-home', 'app_home'),

			MenuItem::section(new TranslatableMessage('menu.game.index', [], 'admin')),
			MenuItem::linkToCrud(new TranslatableMessage('menu.game.index', [], 'admin'), 'fa-solid fa-gamepad', Game::class),
			MenuItem::linkToCrud(new TranslatableMessage('menu.game.gender', [], 'admin'), 'fa-solid fa-icons', Genre::class),
			MenuItem::linkToCrud(new TranslatableMessage('menu.game.rating', [], 'admin'), 'fa-solid fa-star', Rating::class),

			/*MenuItem::section(new TranslatableMessage('menu.category.index', [], 'admin')),
			MenuItem::linkToCrud(new TranslatableMessage('menu.category.platform', [], 'admin'), 'fa-solid fa-gamepad', Plateform::class),*/

			MenuItem::section(new TranslatableMessage('menu.store.index', [], 'admin')),
			MenuItem::linkToCrud(new TranslatableMessage('menu.store.users.index', [], 'admin'), 'fa-solid fa-user-gear', User::class),
			MenuItem::linkToCrud(new TranslatableMessage('menu.store.orders', [], 'admin'), 'fa-solid fa-laptop', Purchase::class),
			MenuItem::linkToCrud(new TranslatableMessage('menu.store.stocks', [], 'admin'), 'fa-solid fa-shop', Stock::class),

			MenuItem::section(new TranslatableMessage('menu.marketing.index', [], 'admin')),
			MenuItem::linkToCrud(new TranslatableMessage('menu.marketing.newsletter', [], 'admin'), 'fa-solid fa-paper-plane', Newsletter::class),
			MenuItem::linkToCrud(new TranslatableMessage('menu.marketing.registered_users', [], 'admin'), 'fa-solid fa-users-gear', NewsletterUser::class),

			/*MenuItem::section(new TranslatableMessage('menu.settings.index', [], 'admin'))->setPermission('ROLE_ADMIN'),
			MenuItem::linktoRoute(new TranslatableMessage('menu.settings.maintenance', [], 'admin'), 'fa-solid fa-shield-halved', 'app_admin')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),
			MenuItem::linkToRoute(new TranslatableMessage('menu.settings.site_management', [], 'admin'), 'fa-solid fa-user-tie', 'app_admin')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),
//			MenuItem::linkToRoute(new TranslatableMessage('menu.settings.method_of_payment', [], 'admin'), 'fa-solid fa-money-check', '')->setPermission('ROLE_ADMIN')->setBadge('Not up', 'danger'),
			MenuItem::linkToRoute(new TranslatableMessage('menu.settings.administrators', [], 'admin'), 'fa-solid fa-user-tie', 'app_admin')->setPermission('ROLE_ADMIN')->setBadge('<X>', 'danger'),*/
		];
	}
}
