<?php

namespace App\Data;

class SearchData
{
	public int $page = 1;

	public string|null $q = '';

	public array $genres = [];

	public null|int $min;

	public null|int $max;

	public bool $preorder = false;

}