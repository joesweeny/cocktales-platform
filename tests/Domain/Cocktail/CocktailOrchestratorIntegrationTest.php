<?php

namespace Cocktales\Domain\Cocktail;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Domain\Cocktail\Persistence\Repository;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class CocktailOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  CocktailOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_insert_cocktail_increases_table_count()
    {
        $this->orchestrator->createCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $total = $this->connection->table('cocktail')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->createCocktail(new Cocktail(
            new Uuid('11f3bf10-6d57-4262-96bd-a7285171325d'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Cosmopolitan'
        ));

        $total = $this->connection->table('cocktail')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_insert_a_cocktail_that_already_exists()
    {
        $this->orchestrator->createCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage('Cocktail with ID 0487d724-4ca0-4942-bf64-4cc53273bc2b already exists');
        $this->orchestrator->createCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));
    }

    public function test_cocktail_can_be_retrieved_by_id()
    {
        $this->orchestrator->createCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed'));

        $fetched = $this->orchestrator->getCocktailById(new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'));

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) $fetched->getId());
        $this->assertEquals('The Titty Twister', $fetched->getName());
        $this->assertEquals('Made in my garage when pissed', $fetched->getOrigin());
    }

    public function test_exception_thrown_if_attempting_to_retrieve_a_cocktail_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cocktail with ID 0487d724-4ca0-4942-bf64-4cc53273bc2b does not exist');
        $this->orchestrator->getCocktailById(new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'));
    }

    public function test_get_cocktails_by_user_id_returns_a_collection_of_cocktails_with_associated_user_id()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->orchestrator->createCocktail((new Cocktail(
                Uuid::generate(),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                "Cocktail $i"
            ))->setOrigin('Made in my garage when pissed'));
        }

        $fetched = $this->orchestrator->getCocktailsByUserId(new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'));

        $this->assertCount(4, $fetched);

        foreach ($fetched as $cocktail) {
            $this->assertInstanceOf(Cocktail::class, $cocktail);
            $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $cocktail->getUserId());
        }
    }
}