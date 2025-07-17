<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;

class TextSeo extends Validator
{
    public const string INVALID = 'invalid';

    protected array $messages = [
        self::INVALID => 'Dozwolone znaki: A-Z, 0-9, _-\':().',
    ];

    public function isValid($value): bool
    {
        $value = (string)$value;

        if (!preg_match('/^([\w\d \-–\'.,?!:()&])+$/ui', $value)) {
            return $this->error(self::INVALID);
        }

        return true;
    }
}