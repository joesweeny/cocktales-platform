<?php

namespace Cocktales\Domain\Cocktail\Hydration;

use Cocktales\Domain\Cocktail\Entity\Cocktail;

class Extractor
{
    public static function toRawData(Cocktail $cocktail): \stdClass
    {
        return (object) [
            'id' => $cocktail->getId()->toBinary(),
            'user_id' => $cocktail->getUserId()->toBinary(),
            'name' => $cocktail->getName(),
            'origin' => $cocktail->getOrigin(),
            'created_at' => $cocktail->getCreatedDate()->getTimestamp()
        ];
    }
}
