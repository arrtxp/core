<?php

namespace Core\Initializers;

use Core\Handler;
use Core\InputFilters;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;

class Basic implements InitializerInterface
{
    public function __invoke(ContainerInterface|PsrContainerInterface $container, $instance)
    {
        if ($instance instanceof Handler) {
            $instance->setInputFilters($container->get(InputFilters::class));
        }
    }
}