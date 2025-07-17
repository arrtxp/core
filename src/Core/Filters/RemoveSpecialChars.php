<?php

namespace Arrtxp\Core\Filters;

use Arrtxp\Core\Filter;
use Transliterator;

class RemoveSpecialChars extends Filter
{
    public function filter($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string)$value;

        $tranliterator = Transliterator::create('NFD; Remove; NFC');
        $value = $tranliterator->transliterate($value);

        return trim(strip_tags($value));
    }
}