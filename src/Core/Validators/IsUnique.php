<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class IsUnique extends Validator
{
    public const string ERROR_INVALID = 'invalid';

    public const string OPTION_HAYSTACK = 'haystack';

    protected array $messages = [
        self::ERROR_INVALID => 'Wartość powinna być unikalna',
    ];

    protected array $haystack;

    public function isValid($value): bool
    {
        $count = array_filter($this->haystack, static fn($item) => $item === $value);

        if (count($count) > 1) {
            return $this->error(self::ERROR_INVALID);
        }

        return true;
    }
}