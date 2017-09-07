<?php

namespace Cocktales\Domain\CocktailIngredient;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Ingredient\Entity\Ingredient;

class CocktailIngredientPresenter
{
    public function toDto(CocktailIngredient $cocktailIngredient, Ingredient $ingredient): \stdClass
    {
        return (object) [
            'name' => $ingredient->getName(),
            'order_number' => $cocktailIngredient->getOrderNumber(),
            'quantity' => $cocktailIngredient->getQuantity(),
            'measurement' => $cocktailIngredient->getMeasurement()
        ];
    }
}
