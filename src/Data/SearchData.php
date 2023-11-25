<?php

namespace App\Data;

class SearchData // Création d'une classe SearchData qui va contenir les données de recherche
{
	public int $page = 1; // Page en cours

	public string|null $q = ''; // Recherche par nom

	public array $genres = []; // Recherche par genre

	public int|null $min; // Prix minimum

	public int|null $max; // Prix maximum

	public bool $preorder = false; // Recherche par précommande

	public bool $promotion = false; // Recherche par promotion

}