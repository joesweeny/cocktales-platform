<?php

namespace Cocktales\Testing\Traits;

use Interop\Container\ContainerInterface;
use Cocktales\Bootstrap\Config;
use Cocktales\Bootstrap\ConfigFactory;
use Cocktales\Bootstrap\ContainerFactory;

trait UsesContainer
{
    protected function createContainer(): ContainerInterface
    {
        return (new ContainerFactory)->create(ConfigFactory::create()->set('database.default.pdo.dsn', 'sqlite::memory:')
        );
    }
}
