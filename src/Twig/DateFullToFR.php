<?php

namespace App\Twig;

use DateTime;
use DateTimeImmutable;
use IntlDateFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateFullToFR extends AbstractExtension
{
    //	getFunctions permet de récupérer dans le fichier twig le nom de la fonction et la fonction à appeler (dateFR)
    public function getFunctions (): array
    {
        return [new TwigFunction("dateFR", [$this, "dateFR"]), new TwigFunction("fullDateFR", [$this, "fullDateFR"]),];
    }

    // fonction permettant de formater la date en français et en toute lettre
    public function dateFR (DateTime $date): string
    {
        $formatter = new IntlDateFormatter("fr_FR", IntlDateFormatter::LONG, IntlDateFormatter::NONE);

        return $formatter->format($date);
    }

    /*
     * fonction permettant de formater la date en français et en toute lettre
     */
    public function fullDateFR (DateTimeImmutable $date): string
    {
        $formatter = new IntlDateFormatter("fr_FR", IntlDateFormatter::LONG, IntlDateFormatter::SHORT);

        return $formatter->format($date);
    }
}
