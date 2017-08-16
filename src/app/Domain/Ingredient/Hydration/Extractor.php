<?php

namespace Cocktales\Domain\Ingredient\Hydration;

use Cocktales\Domain\Ingredient\Entity\Ingredient;

class Extractor
{
    /**
     * @param Ingredient $ingredient
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     */
    public static function toRawData(Ingredient $ingredient): \stdClass
    {
        return (object) [
            'id' => $ingredient->getId()->toBinary(),
            'name' => $ingredient->getName(),
            'category' => $ingredient->getCategory()->getValue(),
            'type' => $ingredient->getType()->getValue(),
            'created_at' => $ingredient->getCreatedDate()->getTimestamp(),
            'updated_at' => $ingredient->getLastModifiedDate()->getTimestamp()
        ];
    }
}
