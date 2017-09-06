<?php

namespace Cocktales\Domain\CocktailIngredient\Hydration;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;

class Extractor
{
    public static function toRawData(CocktailIngredient $ingredient): \stdClass
    {
        return (object) [
            'cocktail_id' => $ingredient->getCocktailId()->toBinary(),
            'ingredient_id' => $ingredient->getIngredientId()->toBinary(),
            'order_number' => $ingredient->getOrderNumber(),
            'quantity' => $ingredient->getQuantity(),
            'measurement' => $ingredient->getMeasurement()
        ];
    }
}
