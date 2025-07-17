<?php

namespace Core\Validators;

use Core\Validator;

class Between extends Validator
{
    public const string INVALID_MIN = 'invalidMin';
    public const string INVALID_MAX = 'invalidMax';

    public const string OPTION_MIN = 'min';
    public const string OPTION_MAX = 'max';

    protected ?float $min;
    protected ?float $max;

    public function __construct(array $options = [])
    {
        $this->messages = [
            self::INVALID_MIN => 'Min. wartość to %s.',
            self::INVALID_MAX => 'Max. wartość to %s.',
        ];
    }

    public function isValid($value): bool
    {
        $value = (int)$value;

        if (!isset($this->min) && !isset($this->max)) {
            return $this->error(self::INVALID_MIN, 0);
        }

        if (isset($this->min) && $value < $this->min) {
            return $this->error(self::INVALID_MIN, $this->min);
        }

        if (isset($this->max) && $value > $this->max) {
            return $this->error(self::INVALID_MAX, $this->max);
        }

        return true;
    }
}