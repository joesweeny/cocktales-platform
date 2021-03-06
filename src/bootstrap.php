<?php

use Cocktales\Bootstrap\ConfigFactory;
use Cocktales\Bootstrap\ContainerFactory;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    (new \josegonzalez\Dotenv\Loader(__DIR__ . '/.env'))
        ->parse()
        ->putenv();
}

return (new ContainerFactory)->create(ConfigFactory::create());
