<?php

namespace Core\Validators;

use Core\Validator;

class MaxSizeFile extends Validator
{
    public const string INVALID = 'invalid';

    public const string OPTION_MAX = 'max';

    protected float $max;

    public function __construct(array $options = [])
    {
        $this->messages = [
            self::INVALID => 'Maksymalny rozmiar pliku to %s MB.',
        ];
    }

    public function isValid($value): bool
    {
        $value = (float)$value;
        $value = round($value / 1000000, 4, PHP_ROUND_HALF_UP);

        if ($value <= 0 || $value > $this->max) {
            return $this->error(self::INVALID, $this->max);
        }

        return true;
    }
}