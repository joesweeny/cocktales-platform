<?php

namespace Cocktales\Boundary\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;

class IngredientPresenter
{
    public function toDto(Ingredient $ingredient): \stdClass
    {
        return (object) [
            'id' => $ingredient->getId()->__toString(),
            'name' => $ingredient->getName(),
            'category' => $ingredient->getCategory()->getValue(),
            'type' => $ingredient->getType()->getValue()
        ];
    }
}
