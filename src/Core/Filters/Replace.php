<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;

class Replace extends Filter
{
    public const OPTION_SEARCH = 'search';
    public const OPTION_REPLACE = 'replace';

    protected string $search;
    protected string $replace;

    public function filter($value): string
    {
        return str_replace($this->search, $this->replace, $value);
    }
}