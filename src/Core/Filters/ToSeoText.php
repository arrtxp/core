<?php

namespace Core\Filters;

use Core\Filter;

class ToSeoText extends Filter
{
    public function filter($value): string
    {
        $value = (string)$value;
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $value = preg_replace("/[^\w -]/ui", "", $value);
        $value = preg_replace('/\s+/', ' ', $value);
        $value = str_replace(['?', ' ', '/', ',', '&'], "-", $value);
        $value = preg_replace('/-+/', '-', $value);

        return trim(strtolower($value));
    }
}