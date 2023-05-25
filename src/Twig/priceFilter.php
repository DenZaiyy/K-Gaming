<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class priceFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'price'])
        ];

    }

    public function price($price, string $symbol = '€', string $separator = ',', string $secondSeparator = ' '): string
    {
//        $finalPrice = $price / 100;
        $finalPrice = $price;
        $finalPrice = number_format($finalPrice, 2, $separator, $secondSeparator);

        return $finalPrice . ' '. $symbol;
    }
}