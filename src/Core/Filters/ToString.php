<?php

namespace Core\Filters;

use Core\Filter;

class ToString extends Filter
{
    public function filter($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (!is_string($value) && !is_numeric($value)) {
            return "";
        }

        return trim((string)$value);
    }
}
