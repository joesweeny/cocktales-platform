<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
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
    /** @var  \Cocktales\Domain\Ingredient\Persistence\Repository */
    private $ingredientRepo;
    /** @var  \Cocktales\Domain\CocktailIngredient\Persistence\Repository */
    private $ciRepo;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->repository = $this->container->get(Repository::class);
        $this->connection = $this->container->get(Connection::class);
        $this->ingredientRepo = $this->container->get(\Cocktales\Domain\Ingredient\Persistence\Repository::class);
        $this->ciRepo = $this->container->get(\Cocktales\Domain\CocktailIngredient\Persistence\Repository::class);
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

    public function test_exception_is_thrown_if_attempting_to_insert_a_cocktail_that_already_exists()
    {
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Cocktail with ID 0487d724-4ca0-4942-bf64-4cc53273bc2b already exists');
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));
    }

    public function test_cocktail_can_be_retrieved_by_id()
    {
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $fetched = $this->repository->getCocktailById(new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'));

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) $fetched->getId());
        $this->assertEquals('The Titty Twister', $fetched->getName());
        $this->assertEquals('Made in my garage when pissed', $fetched->getOrigin());
    }

    public function test_exception_thrown_if_attempting_to_retrieve_a_cocktail_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cocktail with ID 0487d724-4ca0-4942-bf64-4cc53273bc2b does not exist');
        $this->repository->getCocktailById(new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'));
    }

    public function test_get_cocktails_by_user_id_returns_a_collection_of_cocktails_with_associated_user_id()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->repository->insertCocktail((new Cocktail(
                Uuid::generate(),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                "Cocktail $i"
            ))->setOrigin('Made in my garage when pissed'));
        }

        $fetched = $this->repository->getCocktailsByUserId(new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'));

        $this->assertCount(4, $fetched);

        foreach ($fetched as $cocktail) {
            $this->assertInstanceOf(Cocktail::class, $cocktail);
            $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $cocktail->getUserId());
        }
    }

    public function test_cocktail_can_be_retrieved_by_name()
    {
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $fetched = $this->repository->getCocktailByName('The Titty Twister');

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) $fetched->getId());
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $fetched->getUserId());
        $this->assertEquals('The Titty Twister', $fetched->getName());
        $this->assertEquals('Made in my garage when pissed', $fetched->getOrigin());
    }

    public function test_exception_is_thrown_if_attempting_to_retrieve_a_cocktail_with_a_name_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Cocktail 'Manhattan' does not exist");
        $this->repository->getCocktailByName('Manhattan');
    }

    public function test_cocktails_with_matching_ingredient_ids_can_be_retrieved_in_order_of_ingredients_matched()
    {
        $this->addIngredients();
        $this->addCocktails();
        $this->mixCocktailIngredients();

        $fetched = $this->repository->getCocktailsMatchingIngredients([
            new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'),
            new Uuid('c16bdd37-c5a3-481a-abe4-fd0426ba561c'),
            new Uuid('7a5fd740-5dd6-49f1-9ea6-ce71e11a057e'),
            new Uuid('3f51bb91-3f9c-459f-8cdd-b0ccf6f4b7ab')
        ]);

        $cocktailsArray = $fetched->toArray();

        $this->assertEquals('The Titty Twister', array_shift($cocktailsArray)->getName());
        $this->assertEquals('Sex on the Beach', array_shift($cocktailsArray)->getName());
        $this->assertEquals('Manhattan', array_shift($cocktailsArray)->getName());
        $this->assertEquals('Woo Woo', array_shift($cocktailsArray)->getName());
    }

    private function addIngredients()
    {
        // Create Ingredients
        $this->ingredientRepo->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->ingredientRepo->insertIngredient((new Ingredient('8b2b4f7e-b042-41f0-84f0-9c089700dd09'))
            ->setName('Smirnoff Black')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->ingredientRepo->insertIngredient((new Ingredient('c16bdd37-c5a3-481a-abe4-fd0426ba561c'))
            ->setName('Orange Juice')
            ->setCategory(Category::MIXER())
            ->setType(Type::FRUIT_JUICE()));

        $this->ingredientRepo->insertIngredient((new Ingredient('7a5fd740-5dd6-49f1-9ea6-ce71e11a057e'))
            ->setName('WKD Blue')
            ->setCategory(Category::MIXER())
            ->setType(Type::ALCOPOP()));

        $this->ingredientRepo->insertIngredient((new Ingredient('036c8c2b-7635-43fa-96e5-a093117d97a5'))
            ->setName('Barefoot Rose')
            ->setCategory(Category::WINE())
            ->setType(Type::ROSE_WINE()));

        $this->ingredientRepo->insertIngredient((new Ingredient('94bc0395-ef13-4b32-9224-ac8f53d3c54e'))
            ->setName("Stag's Breath")
            ->setCategory(Category::LIQUEUR())
            ->setType(Type::WHISKEY()));

        $this->ingredientRepo->insertIngredient((new Ingredient('3f51bb91-3f9c-459f-8cdd-b0ccf6f4b7ab'))
            ->setName('Budweiser')
            ->setCategory(Category::BEER())
            ->setType(Type::LAGER()));

        $this->ingredientRepo->insertIngredient((new Ingredient('95de8bde-8e44-4403-8f5b-d71658f8eaf0'))
            ->setName('Kopperberg Mixed Fruits')
            ->setCategory(Category::CIDER())
            ->setType(Type::BERRY_CIDER()));

        $this->ingredientRepo->insertIngredient((new Ingredient('50e245a0-9a58-4b32-8ec6-a1a9bbf9ab09'))
            ->setName('Moet et Chandon')
            ->setCategory(Category::CHAMPAGNE())
            ->setType(Type::CHAMPAGNE()));
    }

    private function addCocktails()
    {
        // Create Cocktails
        $this->repository->insertCocktail((new Cocktail(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $this->repository->insertCocktail((new Cocktail(
            new Uuid('4e170e12-875e-4a9e-9f46-b1e51f56ef7e'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Sex on the Beach'
        ))->setOrigin('Made in my garage when pissed'));

        $this->repository->insertCocktail((new Cocktail(
            new Uuid('f0cd4318-948c-4aef-8f1a-b911cc54ebb9'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Manhattan'
        ))->setOrigin('Made in my garage when pissed'));

        $this->repository->insertCocktail((new Cocktail(
            new Uuid('59a5341a-eed4-44cd-ac97-1bd28021a468'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Woo Woo'
        ))->setOrigin('Made in my garage when pissed'));
    }

    public function mixCocktailIngredients()
    {
        // Titty Twister
        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'),
            1,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('c16bdd37-c5a3-481a-abe4-fd0426ba561c'),
            2,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('7a5fd740-5dd6-49f1-9ea6-ce71e11a057e'),
            3,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('3f51bb91-3f9c-459f-8cdd-b0ccf6f4b7ab'),
            4,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('33814097-be87-4d20-b49b-0034893ca67a'),
            new Uuid('50e245a0-9a58-4b32-8ec6-a1a9bbf9ab09'),
            5,
            50,
            'ml'
        ));

        // Sex on the Beach
        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('4e170e12-875e-4a9e-9f46-b1e51f56ef7e'),
            new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'),
            1,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('4e170e12-875e-4a9e-9f46-b1e51f56ef7e'),
            new Uuid('8b2b4f7e-b042-41f0-84f0-9c089700dd09'),
            2,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('4e170e12-875e-4a9e-9f46-b1e51f56ef7e'),
            new Uuid('c16bdd37-c5a3-481a-abe4-fd0426ba561c'),
            3,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('4e170e12-875e-4a9e-9f46-b1e51f56ef7e'),
            new Uuid('7a5fd740-5dd6-49f1-9ea6-ce71e11a057e'),
            4,
            50,
            'ml'
        ));

        // Manhattan
        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('f0cd4318-948c-4aef-8f1a-b911cc54ebb9'),
            new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'),
            1,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('f0cd4318-948c-4aef-8f1a-b911cc54ebb9'),
            new Uuid('c16bdd37-c5a3-481a-abe4-fd0426ba561c'),
            2,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('f0cd4318-948c-4aef-8f1a-b911cc54ebb9'),
            new Uuid('036c8c2b-7635-43fa-96e5-a093117d97a5'),
            3,
            50,
            'ml'
        ));

        // Woo Woo
        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('59a5341a-eed4-44cd-ac97-1bd28021a468'),
            new Uuid('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'),
            1,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('59a5341a-eed4-44cd-ac97-1bd28021a468'),
            new Uuid('036c8c2b-7635-43fa-96e5-a093117d97a5'),
            2,
            50,
            'ml'
        ));

        $this->ciRepo->insertCocktailIngredient(new CocktailIngredient(
            new Uuid('59a5341a-eed4-44cd-ac97-1bd28021a468'),
            new Uuid('94bc0395-ef13-4b32-9224-ac8f53d3c54e'),
            3,
            50,
            'ml'
        ));
    }
}
