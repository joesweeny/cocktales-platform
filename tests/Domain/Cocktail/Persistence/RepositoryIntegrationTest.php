<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class RepositoryIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Repository */
    private $repository;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->repository = $this->container->get(Repository::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Repository::class, $this->repository);
    }

    public function test_insert_cocktail_increases_table_count()
    {
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $total = $this->connection->table('cocktail')->get();

        $this->assertCount(1, $total);

        $this->repository->insertCocktail(new Cocktail(
            new Uuid('11f3bf10-6d57-4262-96bd-a7285171325d'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Cosmopolitan'
        ));

        $total = $this->connection->table('cocktail')->get();

        $this->assertCount(2, $total);
    }
}
