<?php

namespace Arrtxp\Core;

use Attribute;

#[Attribute]
readonly class DTOCollections
{
    public function __construct(
        public ?string $name = null,
        ?string $messageRequired = null,
        ?string $messageEmpty = null,
        ?bool $required = null,
        ?bool $allowEmpty = null,
    ) {
    }
}