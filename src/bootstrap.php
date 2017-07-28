<?php

use Cocktales\Bootstrap\ConfigFactory;
use Cocktales\Bootstrap\ContainerFactory;

require __DIR__ . '/vendor/autoload.php';

return (new ContainerFactory)->create(ConfigFactory::create());
