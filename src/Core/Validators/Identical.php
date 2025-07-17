<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class Identical extends Validator
{
    public const string NOT_SAME = 'notSame';

    public const string OPTION_TOKEN = 'token';

    protected array $messages = [
        self::NOT_SAME => 'Błędna wartość.',
    ];

    protected string|int|float $token;

    public function isValid($value): bool
    {
        if ((string)$value !== (string)$this->token) {
            return $this->error(self::NOT_SAME);
        }

        return true;
    }
}