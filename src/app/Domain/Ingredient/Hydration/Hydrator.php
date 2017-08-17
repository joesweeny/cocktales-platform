<?php

namespace Cocktales\Domain\Ingredient\Hydration;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    /**
     * @param \stdClass $data
     * @return Ingredient
     * @throws \UnexpectedValueException
     */
    public static function fromRawData(\stdClass $data): Ingredient
    {
        return (new Ingredient(Uuid::createFromBinary($data->id)))
            ->setName($data->name)
            ->setCategory(new Category($data->category))
            ->setType(new Type($data->type))
            ->setCreatedDate((new \DateTimeImmutable)->setTimestamp($data->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable)->setTimestamp($data->updated_at));
    }
}
