<?php

namespace Core\Validators;

use Core\Validator;
use Throwable;

class Image extends Validator
{
    public const string WRONG = 'notExists';

    protected array $messages = [
        self::WRONG => 'Zły obraz.',
    ];

    public function isValid($value): bool
    {
        if (!is_string($value)) {
            return $this->error(self::WRONG);
        }

        try {
            $data = getimagesize($value);

            if (empty($data[0]) || empty($data[1])) {
                return $this->error(self::WRONG);
            }
        } catch (Throwable $e) {
            return $this->error(self::WRONG);
        }

        return true;
    }
}