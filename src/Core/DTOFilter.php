<?php

namespace Arrtxp\Core;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
readonly class DTOFilter
{
    public function __construct(
        public string $name,
        public array $options = []
    ) {
    }
}