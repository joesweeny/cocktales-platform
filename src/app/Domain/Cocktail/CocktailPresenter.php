<?php

namespace Cocktales\Domain\Cocktail;

use Cocktales\Domain\Cocktail\Entity\Cocktail;

class CocktailPresenter
{
    public function toDto(Cocktail $cocktail): \stdClass
    {
        return (object) [
            'id' => (string) $cocktail->getId(),
            'name' => $cocktail->getName(),
            'userId' => (string) $cocktail->getUserId(),
            'origin' => $cocktail->getOrigin(),
            'createdAt' => $cocktail->getCreatedDate()->format('Y-m-d')
        ];
    }
}
