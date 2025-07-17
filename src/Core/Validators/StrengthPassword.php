<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

use function preg_replace;

class StrengthPassword extends Validator
{
    public const string INVALID_LENGTH = 'invalidLength';
    public const string BAD_BUILD = 'badBuild';

    public const OPTIONS_MIN_LENGTH = 'minLength';

    protected int $minLength;

    protected array $messages = [
        self::INVALID_LENGTH => 'Podane hasło jest za krótkie, min. 7 znaków.',
        self::BAD_BUILD => 'Hasło musi zawierać przynajmniej jeden znak specjalny.',
    ];

    public function isValid($value): bool
    {
        if (mb_strlen($value) < $this->minLength) {
            return $this->error(self::INVALID_LENGTH);
        }

        if (preg_replace('/\w/ui', '', $value) === '') {
            return $this->error(self::BAD_BUILD);
        }

        return true;
    }
}