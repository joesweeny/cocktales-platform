<?php

namespace Cocktales\Helpers;

use Cocktales\Application\Console\Console;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

trait RunsMigrations
{
    protected function runMigrations(ContainerInterface $container): ContainerInterface
    {
        $console = $container->get(Console::class);
        $console->run(new StringInput('migrations:migrate --no-interaction --quiet'), $output = new BufferedOutput);

        $output = $output->fetch();

        if ($output) {
            $this->fail("Migrations output something that wasn't expected: \n\n $output");
        }

        return $container;
    }
}
