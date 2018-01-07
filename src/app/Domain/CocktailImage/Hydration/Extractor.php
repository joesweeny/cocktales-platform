<?php

namespace Cocktales\Domain\CocktailImage\Hydration;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;

class Extractor
{
    public static function toRawData(CocktailImage $image): \stdClass
    {
        return (object) [
            'cocktail_id' => $image->getCocktailId()->toBinary(),
            'filename' => $image->getFilename(),
            'created_at' => $image->getCreatedDate()->getTimestamp()
        ];
    }
}
