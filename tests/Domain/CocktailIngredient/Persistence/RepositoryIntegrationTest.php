<?php

namespace Cocktales\Domain\CocktailIngredient\Persistence;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\CocktailIngredient\Exception\RepositoryException;
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

    public function test_insert_cocktail_ingredient_increases_table_count()
    {
        $this->repository->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
            new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
            1,
            50,
            'ml'
        ));

        $total = $this->connection->table('cocktail_ingredient')->get();

        $this->assertCount(1, $total);

        $this->repository->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('12131480-b870-40b7-b11d-f763d49aacf5'),
            new Uuid('1e1d27bc-1ac5-4d94-9ac5-eca76e933e9f'),
            1,
            150,
            'oz'
        ));

        $total = $this->connection->table('cocktail_ingredient')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_thrown_if_attempting_to_insert_a_cocktail_ingredient_that_already_exists()
    {
        $this->repository->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
            new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
            1,
            50,
            'ml'
        ));

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('CocktailIngredient with Cocktail fe8f3ec8-1711-412c-8324-c1e1e5f19454 and Ingredient 73f261d9-234e-4501-a5dc-8f4f0bc0623a already exists');

        $this->repository->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
            new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
            1,
            50,
            'ml'
        ));
    }

    public function test_get_cocktail_ingredients_returns_a_collection_of_cocktail_ingredients_linked_to_associated_cocktail_id()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->repository->insertCocktailIngredient(new CocktailIngredient(
                new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
                Uuid::generate(),
                $i,
                50,
                'ml'
            ));
        }

        $fetched = $this->repository->getCocktailIngredients(new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'));

        $this->assertCount(4, $fetched);

        foreach ($fetched as $item) {
            $this->assertInstanceOf(CocktailIngredient::class, $item);
            $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $item->getCocktailId());
        }
    }
}
