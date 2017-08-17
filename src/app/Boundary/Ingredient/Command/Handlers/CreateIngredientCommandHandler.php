<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\CreateIngredientCommand;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class CreateIngredientCommandHandler
{
    /**
     * @var IngredientOrchestrator
     */
    private $orchestrator;

    /**
     * CreateIngredientCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     */
    public function __construct(IngredientOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param CreateIngredientCommand $command
     * @throws \Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException
     * @return void
     */
    public function handle(CreateIngredientCommand $command)
    {
        $this->orchestrator->insertIngredient(
            (new Ingredient)
                ->setName($command->getName())
                ->setCategory($command->getCategory())
                ->setType($command->getType())
        );
    }
}
