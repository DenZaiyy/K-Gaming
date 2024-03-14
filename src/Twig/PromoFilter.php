<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PromoFilter extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter("promo", [$this, "promo"])
        ];
    }

    public function promo($percentage, string $symbol = "%"): string
    {
        return "-" . $percentage . $symbol;
    }
}
