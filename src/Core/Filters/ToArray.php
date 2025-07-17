<?php

namespace Core\Filters;

use Core\Filter;

class ToArray extends Filter
{
	public function filter($value): ?array
	{
		if ($value === null) {
			return null;
		}

		return (array)$value;
	}
}