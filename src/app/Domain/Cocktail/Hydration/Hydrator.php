<?php

namespace Cocktales\Domain\Cocktail\Hydration;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    public static function fromRawData(\stdClass $data): Cocktail
    {
        return (new Cocktail(
            Uuid::createFromBinary($data->id),
            Uuid::createFromBinary($data->user_id),
            $data->name)
        )->setOrigin($data->origin)
            ->setCreatedDate((new \DateTimeImmutable)
            ->setTimestamp($data->created_at))
            ->setMatchIngredientCount($data->count ?: 0);
    }
}
