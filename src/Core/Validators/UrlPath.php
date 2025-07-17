<?php

namespace Core\Validators;

use Core\Validator;

class UrlPath extends Validator
{
    public const string INVALID = 'invalid';
    public const string INVALID_MIN_LENGTH = 'invalidMinLength';
    public const string INVALID_MAX_LENGTH = 'invalidMaxLength';

    public const string MIN_LENGTH = 'minLength';
    public const string MAX_LENGTH = 'maxLength';

    protected int $minLength = 3;
    protected int $maxLength;

    protected array $messages = [
        self::INVALID => 'Dozwolone znaki: A-Z, 0-9, _-.',
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

        if (!preg_match('/^([\w-])+(\.html)?$/ui', $value)) {
            return $this->error(self::INVALID);
        }

        return true;
    }
}