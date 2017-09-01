<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
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

    public function test_instructions_can_be_saved_to_database()
    {
        $this->repository->insertInstruction(new Instruction(
            new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
            3,
            'Pour over ice'
        ));

        $total = $this->connection->table('cocktail_instruction')->get();

        $this->assertCount(1, $total);

        $this->repository->insertInstruction(new Instruction(
            new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
            4,
            'Drink mate'
        ));

        $total = $this->connection->table('cocktail_instruction')->get();

        $this->assertCount(2, $total);
    }

    public function test_instructions_can_be_retrieved_by_cocktail_id()
    {
        for ($i = 1; $i < 6; $i++) {
            $this->repository->insertInstruction(new Instruction(
                new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
                $i,
                'New Instruction'
            ));
        }

        $instructions = $this->repository->getInstructions(new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'));

        foreach ($instructions as $instruction) {
            $this->assertInstanceOf(Instruction::class, $instruction);
            $this->assertEquals('49c1eb8e-484a-47c3-84de-c516ea9dc29f', (string) $instruction->getCocktailId());
        }
    }
}
