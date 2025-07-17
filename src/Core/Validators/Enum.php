<?php

namespace Core\Validators;

use BackedEnum;
use Core\Validator;

class Enum extends Validator
{
    public const string INVALID = 'invalid';

    public const string OPTION_ENUM= 'enum';
    public const string OPTION_ALLOW_EMPTY = 'allowEmpty';

    protected array $messages = [
        self::INVALID => 'Podaj poprawną wartość.',
    ];

    /** @var BackedEnum $enum */
    protected string $enum;
    protected bool $allowEmpty = false;

    public function isValid($value): bool
    {
        if ($value === null) {
            return true;
        }

        if ($value === '' && $this->allowEmpty) {
            return true;
        }

        $cases = $this->enum::cases();

        foreach ($cases as $case) {
            if ($value === $case->value) {
                return true;
            }
        }

        return $this->error(self::INVALID);
    }
}