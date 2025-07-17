<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;
use DateTime;
use Throwable;

class Date extends Validator
{
    public const string INVALID = 'invalid';
    public const string INVALID_MIN = 'invalidMin';

    public const string OPTION_FORMAT = 'format';
    public const string OPTION_MIN = 'min';

    protected array $messages = [
        self::INVALID => 'Data powinna być w formacie %s.',
        self::INVALID_MIN => 'Możliwe jest wprowadzenie daty od %s',
    ];

    protected string $format;
    protected DateTime $min;

    public function isValid($value): bool
    {
        try {
            $value = str_replace('T', ' ', $value);
            $d = DateTime::createFromFormat($this->format, $value);

            if (!$d || $d->format($this->format) !== $value) {
                return $this->error(self::INVALID, $this->format);
            }

            if (isset($this->min) && $this->min->getTimestamp() > $d->getTimestamp()) {
                return $this->error(self::INVALID_MIN, $this->min->format($this->format));
            }
        } catch (Throwable $e) {
            return $this->error(self::INVALID);
        }

        return true;
    }
}