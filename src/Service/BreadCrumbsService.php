<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\TranslatableMessage;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadCrumbsService extends AbstractController
{
    public function __construct (private readonly Breadcrumbs $breadcrumbs)
    {
    }

    public function BCGenerate (array $category, array $platform, array $game, array $genre): void
    {
        if ($game) {
            $this->breadcrumbs->addRouteItem($game["label"], $game["route"], $game["params"]);
        }

        if ($platform && !$game) {
            $this->breadcrumbs->addRouteItem($platform["label"], $platform["route"], $platform["params"]);
        } else if ($platform) {
            $this->breadcrumbs->prependRouteItem($platform["label"], $platform["route"], $platform["params"]);
        }

        if ($category && !$platform) {
            $this->breadcrumbs->addRouteItem($category["label"], $category["route"], $category["params"]);
        } else if ($category) {
            $this->breadcrumbs->prependRouteItem($category["label"], $category["route"], $category["params"]);
        }

        if ($genre && !$game) {
            $this->breadcrumbs->addRouteItem($genre["label"], $genre["route"], $genre["params"]);
            $this->breadcrumbs->prependRouteItem(new TranslatableMessage("breadcrumb.gender", [], "messages"),
              "genre_list");
        } else if (!$genre && !$game) {
            $this->breadcrumbs->prependRouteItem(new TranslatableMessage("breadcrumb.gender", [], "messages"),
              "genre_list");
        }

        $this->breadcrumbs->prependRouteItem(new TranslatableMessage("breadcrumb.home", [], "messages"), "app_home");
    }
}
