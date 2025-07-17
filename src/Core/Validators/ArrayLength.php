<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class ArrayLength extends Validator
{
    public const string INVALID_MIN = 'invalidMin';
    public const string INVALID_MAX = 'invalidMax';

    public const string OPTION_MIN = 'min';
    public const string OPTION_MAX = 'max';

    protected ?int $min;
    protected ?int $max;

    protected array $messages = [
        self::INVALID_MIN => 'Min. ilość %s.',
        self::INVALID_MAX => 'Max. ilość %s.',
    ];

    public function isValid(mixed $value): bool
    {
        if (!is_array($value)) {
            return $this->error(self::INVALID_MIN, 0);
        }

        $length = count($value);

        if (!isset($this->min) && !isset($this->max)) {
            return $this->error(self::INVALID_MIN, 0);
        }

        if (isset($this->min) && $length < $this->min) {
            return $this->error(self::INVALID_MIN, $this->min);
        }

        if (isset($this->max) && $length > $this->max) {
            return $this->error(self::INVALID_MAX, $this->max);
        }

        return true;
    }
}