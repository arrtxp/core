<?php

namespace Core\Filters;

use Core\Filter;

class Base64decode extends Filter
{
    public function filter(mixed $value): ?string
    {
        try {
            return base64_decode($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}