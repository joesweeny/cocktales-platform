<?php

namespace Cocktales\Boundary\Cocktail\Command;

use Cocktales\Framework\CommandBus\Command;

class GetCocktailsByIngredientsCommand implements Command
{
    /**
     * @var array
     */
    private $ingredientIds;

    public function __construct(array $ingredientIds)
    {
        $this->ingredientIds = $ingredientIds;
    }

    public function getIngredientIds(): array
    {
        return $this->ingredientIds;
    }
}
