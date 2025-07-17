<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class ToSearchText extends Filter
{
    public const OPTION_APPEND = 'append';

    protected string $append = '';

    public function filter($value): ?string
    {
        return trim(preg_replace("/[^\w .\-{$this->append}]/ui", "", $value));
    }
}
