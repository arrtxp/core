<?php

namespace Core\Filters;

use Core\Filter;

class ToNull extends Filter
{
    public function filter($value): mixed
    {
        if ($value === '' || $value === 'null') {
            return null;
        }

        return $value;
    }
}
