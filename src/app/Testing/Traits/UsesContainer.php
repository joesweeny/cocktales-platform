<?php

namespace Cocktales\Testing\Traits;

use Interop\Container\ContainerInterface;
use Cocktales\Bootstrap\Config;
use Cocktales\Bootstrap\ConfigFactory;
use Cocktales\Bootstrap\ContainerFactory;

trait UsesContainer
{
    protected function createContainer(Config $config = null): ContainerInterface
    {
        return (new ContainerFactory)->create($config ?: ConfigFactory::create());
    }
}
