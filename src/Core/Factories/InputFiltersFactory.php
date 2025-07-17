<?php

namespace Arrtxp\Core\Factories;

use Arrtxp\Core\InputFilters;
use Interop\Container\Containerinterface;

class InputFiltersFactory
{
    public function __invoke(ContainerInterface $container): InputFilters
    {
        return new InputFilters($container);
    }
}