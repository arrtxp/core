<?php

namespace Core\Filters;

use Core\Filter;

class ToLower extends Filter
{
    public function filter($value): string
    {
        return mb_strtolower($value);
    }
}