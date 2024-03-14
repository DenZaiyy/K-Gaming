<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter("price", [$this, "price"]),
            new TwigFilter("oldPrice", [$this, "oldPrice"])
        ];
    }

    public function price($price, string $symbol = "€", string $separator = ",", string $secondSeparator = " "): string
    {
        //        $finalPrice = $price / 100;
        $finalPrice = $price;
        $finalPrice = number_format($finalPrice, 2, $separator, $secondSeparator);

        return $finalPrice . " " . $symbol;
    }

    public function oldPrice($price, string $symbol = "€", string $separator = ",", string $secondSeparator = " "): string
    {
        $finalPrice = $price;
        $finalPrice = number_format($finalPrice, 0, $separator, $secondSeparator);

        return $finalPrice . $symbol;
    }
}
