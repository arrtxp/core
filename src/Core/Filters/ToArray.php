<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

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