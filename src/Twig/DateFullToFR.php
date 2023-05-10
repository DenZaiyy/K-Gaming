<?php

namespace App\Twig;

use DateTime;
use IntlDateFormatter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class DateFullToFR extends AbstractExtension
{
	//	getFunctions permet de récupérer dans le fichier twig le nom de la fonction et la fonction à appeler (dateFR)
	public function getFunctions()
	{
		return [
			new TwigFunction('dateFR', [$this, 'dateFR']),
		];
	}

	// fonction permettant de formater la date en français et en toute lettre
	public function dateFR(DateTime $date)
	{
		$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);

		return $formatter->format($date);
	}
}