<?php

namespace Core\Validators;

use Core\Validator;

class EmailAddress extends Validator
{
	public const string INVALID = 'invalid';

	protected array $messages = [
		self::INVALID => 'Nieprawidłowy e-mail.',
	];

	public function isValid($value): bool
	{
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			return $this->error(self::INVALID);
		}

		return true;
	}
}