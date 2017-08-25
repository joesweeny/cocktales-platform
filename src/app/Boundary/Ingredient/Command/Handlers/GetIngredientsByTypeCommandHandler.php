<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsByTypeCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class GetIngredientsByTypeCommandHandler
{
    /**
     * @var IngredientOrchestrator
     */
    private $orchestrator;
    /**
     * @var IngredientPresenter
     */
    private $presenter;

    /**
     * GetIngredientsByTypeCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     * @param IngredientPresenter $presenter
     */
    public function __construct(IngredientOrchestrator $orchestrator, IngredientPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    public function handle(GetIngredientsByTypeCommand $command): array
    {
        return array_map(function (Ingredient $ingredient) {
            return $this->presenter->toDto($ingredient);
        }, $this->orchestrator->getIngredientsByType($command->getType())->toArray());
    }
}
