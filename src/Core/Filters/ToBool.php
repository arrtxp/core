<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

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