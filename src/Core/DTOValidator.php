<?php

namespace Core;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
readonly class DTOValidator
{
    public function __construct(
        public string $name,
        public array $options = [],
        public ?string $key = null,
    ) {
    }
}