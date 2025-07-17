<?php

namespace Core\Validators;

use Core\Validator;

class Different extends Validator
{
    public const string SAME = 'same';

    public const string OPTION_TOKEN = 'token';

    protected array $messages = [
        self::SAME => 'Podana wartość jest nieprawidłowa.',
    ];

    protected string|int|float $token;

    public function isValid($value): bool
    {
        if ((string)$value === (string)$this->token) {
            return $this->error(self::SAME);
        }

        return true;
    }
}