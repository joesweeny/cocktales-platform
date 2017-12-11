<?php

namespace Cocktales\Domain\Ingredient\Persistence;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IlluminateDbIngredientRepositoryIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
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

    public function test_insert_ingredient_increases_table_count()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $total = $this->connection->table('ingredient')->get();

        $this->assertCount(1, $total);

        $this->repository->insertIngredient((new Ingredient('6a56edad-530f-4b75-9389-050e2aa3c34a'))
            ->setName('Smirnoff Black')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $total = $this->connection->table('ingredient')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_insert_an_ingredient_into_the_database_with_a_name_that_already_exists()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->expectException(IngredientRepositoryException::class);
        $this->expectExceptionMessage('Ingredient with name Smirnoff Red already exists');

        $this->repository->insertIngredient((new Ingredient('6a56edad-530f-4b75-9389-050e2aa3c34a'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));
    }

    public function test_get_ingredients_returns_a_collection_of_all_ingredients_sorted_alphabetically_by_name()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->repository->insertIngredient((new Ingredient('6a56edad-530f-4b75-9389-050e2aa3c34a'))
            ->setName('Bacardi Breezer')
            ->setCategory(Category::MIXER())
            ->setType(Type::ALCOPOP()));

        $this->repository->insertIngredient((new Ingredient('8248febd-7d92-4ea0-947f-179f320310c8'))
            ->setName("Gordon's Gin")
            ->setCategory(Category::SPIRIT())
            ->setType(Type::GIN()));

        $this->repository->insertIngredient((new Ingredient('a4a93668-6e61-4a81-93b4-b2404dbe9788'))
            ->setName('Orange Juice')
            ->setCategory(Category::MIXER())
            ->setType(Type::FRUIT_JUICE()));

        $ingredients = $this->connection->table('ingredient')->get();

        $this->assertEquals('Smirnoff Red', $ingredients[0]->name);
        $this->assertEquals('Bacardi Breezer', $ingredients[1]->name);
        $this->assertEquals("Gordon's Gin", $ingredients[2]->name);
        $this->assertEquals('Orange Juice', $ingredients[3]->name);

        $sortedIngredients = $this->repository->getIngredients();

        $this->assertEquals('Bacardi Breezer', $sortedIngredients[0]->getName());
        $this->assertEquals("Gordon's Gin", $sortedIngredients[1]->getName());
        $this->assertEquals('Orange Juice', $sortedIngredients[2]->getName());
        $this->assertEquals('Smirnoff Red', $sortedIngredients[3]->getName());
    }

    public function test_get_ingredients_by_type_returns_a_collection_of_ingredient_objects_with_specific_type()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->repository->insertIngredient((new Ingredient('a4a93668-6e61-4a81-93b4-b2404dbe9788'))
            ->setName('Smirnoff Black')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->repository->insertIngredient((new Ingredient('8248febd-7d92-4ea0-947f-179f320310c8'))
            ->setName('Orange Juice')
            ->setCategory(Category::MIXER())
            ->setType(Type::FRUIT_JUICE()));

        $ingredients = $this->repository->getIngredientsByType(Type::VODKA());

        $this->assertCount(2, $ingredients);

        foreach ($ingredients as $ingredient) {
            $this->assertEquals(Type::VODKA(), $ingredient->getType());
        }
    }

    public function test_get_ingredients_by_category_returns_a_collection_of_ingredient_objects_with_specific_category()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->repository->insertIngredient((new Ingredient('8248febd-7d92-4ea0-947f-179f320310c8'))
            ->setName('Smirnoff Black')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->repository->insertIngredient((new Ingredient('a4a93668-6e61-4a81-93b4-b2404dbe9788'))
            ->setName('Orange Juice')
            ->setCategory(Category::MIXER())
            ->setType(Type::FRUIT_JUICE()));

        $ingredients = $this->repository->getIngredientsByCategory(Category::SPIRIT());

        $this->assertCount(2, $ingredients);

        foreach ($ingredients as $ingredient) {
            $this->assertEquals(Category::SPIRIT(), $ingredient->getCategory());
        }
    }

    public function test_ingredient_can_be_retrieved_by_id()
    {
        $this->repository->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $ingredient = $this->repository->getIngredientById(new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'));

        $this->assertEquals('Smirnoff Red', $ingredient->getName());
        $this->assertEquals(Category::SPIRIT(), $ingredient->getCategory());
        $this->assertEquals(Type::VODKA(), $ingredient->getType());
    }

    public function test_exception_thrown_if_attempting_to_retrieve_an_ingredient_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Ingredient with ID e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5 does not exist');
        $this->repository->getIngredientById(new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'));
    }
}
