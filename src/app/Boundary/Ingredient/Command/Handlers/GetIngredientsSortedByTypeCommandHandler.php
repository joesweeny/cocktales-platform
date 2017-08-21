<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByTypeCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class GetIngredientsSortedByTypeCommandHandler
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
     * GetIngredientsSortedByTypeCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     * @param IngredientPresenter $presenter
     */
    public function __construct(IngredientOrchestrator $orchestrator, IngredientPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetIngredientsSortedByTypeCommand $command
     * @return array|\stdClass[]
     */
    public function handle(GetIngredientsSortedByTypeCommand $command): array
    {
        $ingredients = [];

        foreach (Type::toArray() as $key => $value) {
            $ingredients[$key]= array_map(function (Ingredient $ingredient) {
                return $this->presenter->toDto($ingredient);
            }, $this->orchestrator->getIngredientsByType(new Type($value))->toArray());
        }

        return $ingredients;
    }
}
