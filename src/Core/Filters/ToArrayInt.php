<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class ToArrayInt extends Filter
{
    public function filter($value): ?array
    {
        if ($value === null) {
            return null;
        }

        $value = (array)$value;
        foreach ($value as &$item) {
            $item = (int)$item;
        }

        return $value;
    }
}