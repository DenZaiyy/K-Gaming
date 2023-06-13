<?php

namespace App\Data;

class SearchData
{
	public int $page = 1;

	public string|null $q = '';

	public array $genres = [];

	public int|null $min;

	public int|null $max;

	public bool $preorder = false;

}