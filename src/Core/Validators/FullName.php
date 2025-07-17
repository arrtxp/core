<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class FullName extends Validator
{
    public const string INVALID = 'invalid';
    public const string INVALID_FULL_NAME = 'invalidFullName';
    public const string INVALID_MIN_LENGTH = 'invalidMinLength';
    public const string INVALID_MAX_LENGTH = 'invalidMaxLength';

    public const string OPTION_MIN_LENGTH = 'minLength';
    public const string OPTION_MAX_LENGTH = 'maxLength';

    protected int $minLength;
    protected int $maxLength;

    protected array $messages = [
        self::INVALID => 'Dozwolone znaki: A-Z,-.',
        self::INVALID_FULL_NAME => 'Podaj imię i nazwisko.',
        self::INVALID_MIN_LENGTH => 'Min. %s znaki.',
        self::INVALID_MAX_LENGTH => 'Max. %s znaki.',
    ];

    public function isValid($value): bool
    {
        $value = (string)$value;
        $strlen = mb_strlen($value);

        if ($strlen < $this->minLength) {
            return $this->error(self::INVALID_MIN_LENGTH, $this->minLength);
        }

        if ($strlen > $this->maxLength) {
            return $this->error(self::INVALID_MAX_LENGTH, $this->maxLength);
        }

        if (preg_replace('/[^\w\- \D]|[^\D]/ui', '', $value) !== $value) {
            return $this->error(self::INVALID);
        }

        if (count(explode(' ', $value)) <= 1) {
            return $this->error(self::INVALID_FULL_NAME);
        }

        return true;
    }
}