<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsByCategoryCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class GetIngredientsByCategoryCommandHandler
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
     * GetIngredientsByCategoryCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     * @param IngredientPresenter $presenter
     */
    public function __construct(IngredientOrchestrator $orchestrator, IngredientPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    public function handle(GetIngredientsByCategoryCommand $command): array
    {
        return array_map(function (Ingredient $ingredient) {
            return $this->presenter->toDto($ingredient);
        }, $this->orchestrator->getIngredientsByCategory($command->getCategory())->toArray());
    }
}
