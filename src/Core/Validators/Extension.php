<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class Extension extends Validator
{
    public const WRONG = 'wrong';

    public const string OPTION_HAYSTACK = 'haystack';

    protected array $messages = [
        self::WRONG => "Nieprawidłowy format pliku. Dozwolone: '%s'",
    ];

    protected array $haystack;
    protected bool $strict = true;

    public function isValid($value): bool
    {
        $ext = pathinfo($value, PATHINFO_EXTENSION);
        if (!in_array($ext, $this->haystack, false)) {
            return $this->error(self::WRONG, implode("','", $this->haystack));
        }

        return true;
    }
}