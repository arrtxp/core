<?php

namespace Core;

use Core\Traits\BuildFilterOrValidator;

abstract class Filter
{
    use BuildFilterOrValidator;

    abstract public function filter(mixed $value): mixed;
}