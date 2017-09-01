<?php

namespace Cocktales\Domain\Instruction;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class InstructionOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  InstructionOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->orchestrator = $this->container->get(InstructionOrchestrator::class);
    }

    public function test_instructions_can_be_saved_to_database()
    {
        $this->orchestrator->insertInstruction(new Instruction(
            new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
            3,
            'Pour over ice'
        ));

        $total = $this->connection->table('cocktail_instruction')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->insertInstruction(new Instruction(
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
            $this->orchestrator->insertInstruction(new Instruction(
                new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
                $i,
                'New Instruction'
            ));
        }

        $instructions = $this->orchestrator->getInstructions(new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'));

        foreach ($instructions as $instruction) {
            $this->assertInstanceOf(Instruction::class, $instruction);
            $this->assertEquals('49c1eb8e-484a-47c3-84de-c516ea9dc29f', (string) $instruction->getCocktailId());
        }
    }
}
