<?php

namespace Core\Filters;

use Core\Filter;

use const PHP_ROUND_HALF_UP;

class Round extends Filter
{
    public const string OPTION_MODE = 'mode';
    public const string OPTION_PRECISION = 'precision';

    protected int $mode = PHP_ROUND_HALF_UP;
    protected int $precision = 2;

    public function filter($value): float|int
    {
        if ($this->precision === 0) {
            return (int)$value;
        }

        return round($value, $this->precision, $this->mode);
    }
}