<?php

namespace Core\Validators;

use Core\Validator;

class InArray extends Validator
{
    public const string NOT_IN_ARRAY = 'notInArray';

    public const string OPTION_HAYSTACK = 'haystack';

    protected array $messages = [
        self::NOT_IN_ARRAY => 'Podaj poprawną wartość.',
    ];

    protected array $haystack;
    protected bool $strict = true;

    public function isValid($value): bool
    {
        if (!in_array($value, $this->haystack, $this->strict)) {
            return $this->error(self::NOT_IN_ARRAY);
        }

        return true;
    }
}