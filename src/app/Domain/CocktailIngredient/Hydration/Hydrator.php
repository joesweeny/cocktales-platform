<?php

namespace Cocktales\Domain\CocktailIngredient\Hydration;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    public static function fromRawData(\stdClass $data): CocktailIngredient
    {
        return new CocktailIngredient(
            Uuid::createFromBinary($data->cocktail_id),
            Uuid::createFromBinary($data->ingredient_id),
            $data->order_number,
            $data->quantity,
            $data->measurement
        );
    }
}
