<?php

namespace Core\Filters;

use Core\Filter;
use Core\Utils;
use Throwable;

class ToUuid extends Filter
{
    public function filter($value): ?string
    {
        try {
            if ($value[1] === 'x') {
                $value = substr($value, 2);
            }

            return Utils::uuid($value);
        } catch (Throwable $e) {
            return $value;
        }
    }
}