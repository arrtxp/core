<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class StripTags extends Filter
{
    public function filter($value): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return trim(strip_tags((string)$value));
    }
}