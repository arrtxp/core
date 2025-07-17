<?php

namespace Core\Validators;

use Core\Session;
use Core\Utils;
use Core\Validator;

class Csrf extends Validator
{
	public const string INVALID = 'invalid';

	protected array $messages = [
		self::INVALID => 'Wystąpił nieoczekiwany błąd, spróbuj ponownie.',
	];

	public function __construct(
		private readonly Session $session
	) {

	}

	public function isValid($value): bool
	{
		if (Utils::isAjaxRequest()) {
			return true;
		}

		if ($value !== $this->session->csrf) {
			return $this->error(self::INVALID);
		}

		$this->session->csrf = null;

		return true;
	}
}