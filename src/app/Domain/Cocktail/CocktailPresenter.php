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
            'user_id' => (string) $cocktail->getUserId(),
            'origin' => $cocktail->getOrigin(),
            'created_at' => $cocktail->getCreatedDate()->format('Y-m-d')
        ];
    }
}
