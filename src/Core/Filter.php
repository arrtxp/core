<?php

namespace Arrtxp\Core;

use Arrtxp\Core\Traits\BuildFilterOrValidator;

abstract class Filter
{
    use BuildFilterOrValidator;

    abstract public function filter(mixed $value): mixed;
}