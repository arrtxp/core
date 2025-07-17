<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class ToFloat extends Filter
{
    public const string OPTION_ALL = 'all';

    protected bool $all = false;

    public function filter($value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (!$this->all && ($value === null || $value === '')) {
            return null;
        }

        return (float)str_replace([' ', ','], ['', '.'], $value);
    }
}
