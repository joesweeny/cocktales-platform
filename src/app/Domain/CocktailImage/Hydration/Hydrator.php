<?php

namespace Cocktales\Domain\CocktailImage\Hydration;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    public static function fromRawData(\stdClass $data): CocktailImage
    {
        return (new CocktailImage(
            Uuid::createFromBinary($data->cocktail_id),
            $data->filename
        ))->setCreatedDate((new \DateTimeImmutable)->setTimestamp($data->created_at));
    }
}
