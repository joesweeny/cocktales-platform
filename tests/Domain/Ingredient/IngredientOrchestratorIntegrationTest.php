<?php

namespace Cocktales\Domain\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IngredientOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  IngredientOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->orchestrator = $this->container->get(IngredientOrchestrator::class);
    }

    public function test_insert_ingredient_increases_table_count()
    {
        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $total = $this->connection->table('ingredient')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Black')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $total = $this->connection->table('ingredient')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_insert_an_ingredient_into_the_database_with_a_name_that_already_exists()
    {
        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->expectException(IngredientRepositoryException::class);
        $this->expectExceptionMessage('Ingredient with name Smirnoff Red already exists');

        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));
    }
}
