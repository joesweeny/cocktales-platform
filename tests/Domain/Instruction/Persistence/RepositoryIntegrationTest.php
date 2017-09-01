<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Connection;
use PHPUnit\Framework\TestCase;

class RepositoryIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  Container */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  Repository */
    private $repository;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->repository = $this->container->get(Repository::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Repository::class, $this->repository);
    }
}
