<?php

namespace Core\Filters;

use Core\Filter;
use Laminas\Filter\AbstractFilter;

class ToBool extends Filter
{
	public function filter($value): bool
	{
		if (strtolower($value) === 'false') {
			return false;
		}

		if (strtolower($value) === 'true') {
			return true;
		}

		return (bool)$value;
	}
}