<?php

namespace Core;

use Core\Traits\BuildFilterOrValidator;

abstract class Validator
{
    use BuildFilterOrValidator {
        BuildFilterOrValidator::build as buildParent;
    }

    public const OPTION_MESSAGES = 'messages';

    protected array $messages;
    protected array $errors;

    protected abstract function isValid(mixed $value): bool;

    public function build(): self
    {
        $this->errors = [];

        return $this->buildParent();
    }

    protected function error(string $messageKey, mixed $values = null): false
    {
        $msg = $this->messages[$messageKey];

        if ($values !== null) {
            $values = (array)$values;
            $this->errors[$messageKey] = sprintf($msg, ...$values);
        } else {
            $this->errors[$messageKey] = $msg;
        }

        return false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}