<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class NotEmpty extends Validator
{
    public const string EMPTY = 'empty';
    public const string EMPTY_ARRAY = 'emptyArray';

    protected array $messages = [
        self::EMPTY => 'Pole nie może być puste.',
        self::EMPTY_ARRAY => 'Wybierz jedną z opcji.',
    ];

    public function isValid($value): bool
    {
        if (empty($value)) {
            return $this->error(is_array($value) ? self::EMPTY_ARRAY : self::EMPTY);
        }

        return true;
    }
}