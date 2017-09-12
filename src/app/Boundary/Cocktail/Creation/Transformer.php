<?php

namespace Cocktales\Boundary\Cocktail\Creation;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Framework\Uuid\Uuid;

class Transformer
{
    public function toCocktailIngredient(\stdClass $raw): CocktailIngredient
    {
        return new CocktailIngredient(
            new Uuid($raw->cocktailId),
            new Uuid($raw->ingredientId),
            $raw->orderNumber,
            $raw->quantity,
            $raw->measurement
        );
    }
}
