<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class ToInt extends Filter
{
    public const string OPTION_ALL = 'all';

    protected bool $all = false;

    public function filter($value): ?int
    {
        if (!$this->all && ($value === null || $value === '')) {
            return null;
        }

        return (int)str_replace(' ', '', $value);
    }
}
