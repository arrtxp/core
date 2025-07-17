<?php

namespace Core\Filters;

use Core\Filter;
use Throwable;
use Ramsey\Uuid\Uuid;

class ToUuid extends Filter
{
    public function filter($value): ?string
    {
        try {
            if ($value[1] === 'x') {
                $value = substr($value, 2);
            }

            return Uuid::fromString($value)->toString();
        } catch (Throwable $e) {
            return $value;
        }
    }
}