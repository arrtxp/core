<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class Name extends Validator
{
    public const string INVALID = 'invalid';

    protected array $messages = [
        self::INVALID => 'Dozwolone znaki: A-Z,-.',
    ];

    public function isValid($value): bool
    {
        $value = (string)$value;

        if (preg_replace('/[^\w\- \D]|[^\D]/ui', '', $value) !== $value) {
            return $this->error(self::INVALID);
        }

        return true;
    }
}