<?php

namespace Cocktales\Testing\Traits;

use Cocktales\Bootstrap\Config;
use Cocktales\Bootstrap\ConfigFactory;

trait UsesConfig
{
    protected function createConfig(): Config
    {
        return ConfigFactory::create();
    }
}
