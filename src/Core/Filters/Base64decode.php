<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;
use Throwable;

class Base64decode extends Filter
{
    public function filter(mixed $value): ?string
    {
        try {
            return base64_decode($value);
        } catch (Throwable $e) {
            return null;
        }
    }
}