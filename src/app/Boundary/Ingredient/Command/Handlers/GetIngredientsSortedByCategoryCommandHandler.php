<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByCategoryCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;

class GetIngredientsSortedByCategoryCommandHandler
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
     * GetIngredientsSortedByCategoryCommandHandler constructor.
     * @param IngredientOrchestrator $orchestrator
     * @param IngredientPresenter $presenter
     */
    public function __construct(IngredientOrchestrator $orchestrator, IngredientPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetIngredientsSortedByCategoryCommand $command
     * @return array|\stdClass[]
     */
    public function handle(GetIngredientsSortedByCategoryCommand $command): array
    {
        $ingredients = [];

        foreach (Category::toArray() as $key => $value) {
            $ingredients[$key]= array_map(function (Ingredient $ingredient) {
                return $this->presenter->toDto($ingredient);
            }, $this->orchestrator->getIngredientsByCategory(new Category($value))->toArray());
        }

        return $ingredients;
    }
}
