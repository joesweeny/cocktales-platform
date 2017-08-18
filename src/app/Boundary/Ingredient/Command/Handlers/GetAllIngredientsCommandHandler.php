<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetAllIngredientsCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class GetAllIngredientsCommandHandler
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
     * GetAllIngredientsCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     * @param IngredientPresenter $presenter
     */
    public function __construct(IngredientOrchestrator $orchestrator, IngredientPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetAllIngredientsCommand $command
     * @return array|\stdClass[]
     */
    public function handle(GetAllIngredientsCommand $command)
    {
        return array_map(function (Ingredient $ingredient) {
            return $this->presenter->toDto($ingredient);
        }, $this->orchestrator->getIngredients()->toArray());
    }
}
