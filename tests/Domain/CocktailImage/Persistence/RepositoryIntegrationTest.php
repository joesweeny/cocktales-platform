<?php

namespace Cocktales\Domain\CocktailImage\Persistence;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Framework\Exception\NotFoundException;
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

    public function test_insert_image_increases_table_count()
    {
        $this->repository->insertImage(
            new CocktailImage(
                Uuid::generate(),
                'filename.png'
            )
        );

        $total = $this->connection->table('cocktail_image')->get();

        $this->assertCount(1, $total);

        $this->repository->insertImage(
            new CocktailImage(
                Uuid::generate(),
                'filename.png'
            )
        );

        $total = $this->connection->table('cocktail_image')->get();

        $this->assertCount(2, $total);
    }

    public function test_cocktail_image_can_be_retrieved_by_cocktail_id()
    {
        $this->repository->insertImage(
            new CocktailImage(
                $id = Uuid::generate(),
                'filename.png'
            )
        );

        $fetched = $this->repository->getImageByCocktailId($id);

        $this->assertEquals('filename.png', $fetched->getFilename());
    }

    public function test_not_found_exception_is_thrown_if_cocktail_image_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('CocktailImage with cocktail ID 888a675f-dafb-41b5-a7e0-14bb92db47da does not exist');
        $this->repository->getImageByCocktailId(new Uuid('888a675f-dafb-41b5-a7e0-14bb92db47da'));
    }
}
