<?php

namespace App\Twig;

use DateTime;
use DateTimeImmutable;
use IntlDateFormatter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class DateFormatter extends AbstractExtension
{
  //	getFunctions permet de récupérer dans le fichier twig le nom de la fonction et la fonction à appeler (dateFR)

  public function getFunctions(): array
  {
    return [
      new TwigFunction("dateTranslated", [$this, "dateTranslated"]),
      new TwigFunction("fullDateTranslated", [$this, "fullDateTranslated"]),
    ];
  }

  // fonction permettant de formater la date en français et en toute lettre
  public function dateTranslated(DateTime $date, string $lang): string
  {
    if ($lang == "fr") {
      $formatter = new IntlDateFormatter(
        "fr_FR",
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE
      );
    } elseif ($lang == "en") {
      $formatter = new IntlDateFormatter(
        "en_EN",
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE
      );
    } else {
      $formatter = new IntlDateFormatter(
        "fr_FR",
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE
      );
    }

    return $formatter->format($date);
  }

  /*
   * fonction permettant de formater la date en français et en toute lettre
   */
  public function fullDateTranslated(
    DateTimeImmutable $date,
    string $lang
  ): string {
    if ($lang == "fr") {
      $formatter = new IntlDateFormatter(
        "fr_FR",
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT
      );
    } elseif ($lang == "en") {
      $formatter = new IntlDateFormatter(
        "en_EN",
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT
      );
    } else {
      $formatter = new IntlDateFormatter(
        "fr_FR",
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT
      );
    }

    return $formatter->format($date);
  }
}
