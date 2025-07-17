<?php

namespace Core\Validators;

use Core\Validator;
use Ramsey\Uuid\Uuid as RamseyUuid;

class IsUuid extends Validator
{
	public const string INVALID = 'invalid';

	protected array $messages = [
		self::INVALID => 'Błędny identyfikator.',
	];

	public function isValid($value): bool
	{
		if ($value === null) {
            return $this->error(self::INVALID);
		}

        if (!is_string($value) || empty($value)) {
            return $this->error(self::INVALID);
        }

		try {
            RamseyUuid::fromString($value)->getBytes();
		} catch(\Throwable $e) {
		    return $this->error(self::INVALID);
		}

		return true;
	}
}