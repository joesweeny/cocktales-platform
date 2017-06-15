<?php

namespace Cocktales\Helpers;

use Cocktales\Bootstrap\ContainerFactory;
use Interop\Container\ContainerInterface;

trait CreatesContainer
{
    protected function createContainer(): ContainerInterface
    {
        return (new ContainerFactory)->create();
    }
}
