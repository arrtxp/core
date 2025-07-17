<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class ToLower extends Filter
{
    public function filter($value): string
    {
        return mb_strtolower($value);
    }
}