<?php

namespace Core;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class DTOParam
{
    public function __construct(
        ?string $messageRequired = null,
        ?string $messageEmpty = null,
        ?int $min = null,
        ?int $max = null,
    ) {
    }
}