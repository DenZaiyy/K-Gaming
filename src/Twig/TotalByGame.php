<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TotalByGame extends AbstractExtension
{
    /**
     * Fonction twig permettant de calculer le total d'un jeu en fonction de sa quantité
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction("totalByGame", [$this, "totalByGame"])
        ];
    }

    public function totalByGame($price, $qtt, $currency): string
    {
        //        $total = $price * $qtt / 100;
        $total = $price * $qtt;
        $total = number_format($total, 2, ",", " ");

        return $total . " " . $currency;
    }
}
