<?php

namespace Cocktales\Application\Console;

use Cocktales\Application\Console\Command\IngredientCreate;
use Cocktales\Application\Console\Command\IngredientList;
use Cocktales\Application\Console\Command\IngredientListByCategory;
use Cocktales\Application\Console\Command\IngredientListByType;
use Cocktales\Application\Console\Command\ProfileCreate;
use Cocktales\Application\Console\Command\UserList;
use Cocktales\Application\Console\Command\UserProfile;
use Cocktales\Application\Console\Command\UserRegister;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Finder\GlobFinder;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Console constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $app = new Application('CockTales Command Line');

        $app->setAutoExit(false);

        $configHelper = new ConfigurationHelper($this->container->get(Connection::class), $doctrineConfig = new Configuration(
            $this->container->get(Connection::class),
            null,
            new GlobFinder
        ));

        $doctrineConfig->setMigrationsNamespace(__NAMESPACE__ . '\\Migrations');
        $doctrineConfig->setMigrationsDirectory(__DIR__ . '/Migrations');
        $doctrineConfig->setOutputWriter(new OutputWriter(function (string $message) use ($output) {
            $output->writeln($message);
        }));

        $app->setHelperSet(new HelperSet([
            'dialog' => new QuestionHelper(),
            'configuration' => $configHelper
        ]));

        $app->addCommands([
            $this->container->get(IngredientCreate::class),
            $this->container->get(IngredientList::class),
            $this->container->get(IngredientListByCategory::class),
            $this->container->get(IngredientListByType::class),
            $this->container->get(ProfileCreate::class),
            $this->container->get(UserList::class),
            $this->container->get(UserProfile::class),
            $this->container->get(UserRegister::class),

            new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
        ]);

        return $app->run($input, $output);
    }
}
