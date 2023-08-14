<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadCrumbsService extends AbstractController
{
    public function __construct(private readonly Breadcrumbs $breadcrumbs)
    {
    }

    public function BCGenerate(array $category, array $platform, array $game, array $genre): void
    {
        if($game) {
            $this->breadcrumbs->addRouteItem($game['label'], $game['route'], $game['params']);
        }

        if($platform) {
            $this->breadcrumbs->prependRouteItem($platform['label'], $platform['route'], $platform['params']);
        }

        if($category) {
            $this->breadcrumbs->prependRouteItem($category['label'], $category['route'], $category['params']);
        }

        if($genre) {
            $this->breadcrumbs->prependRouteItem($genre['label'], $genre['route'], $genre['params']);
        }

        $this->breadcrumbs->prependRouteItem("Accueil", "app_home");
    }
}
