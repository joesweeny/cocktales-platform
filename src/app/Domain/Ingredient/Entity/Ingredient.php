<?php

namespace Cocktales\Domain\Ingredient\Entity;

use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\Entity\PrivateAttributesTrait;
use Cocktales\Framework\Entity\TimestampedTrait;
use Cocktales\Framework\Identity\IdentifiedByUuidTrait;

class Ingredient
{
    use IdentifiedByUuidTrait;
    use PrivateAttributesTrait;
    use TimestampedTrait;

    public function setName(string $name): Ingredient
    {
        return $this->set('name', $name);
    }

    /**
     * @return string
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     */
    public function getName(): string
    {
        return $this->getOrFail('name');
    }

    public function setCategory(Category $category): Ingredient
    {
        return $this->set('category', $category);
    }

    /**
     * @return Category
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     */
    public function getCategory(): Category
    {
        return $this->getOrFail('category');
    }

    public function setType(Type $type): Ingredient
    {
        return $this->set('type', $type);
    }

    /**
     * @return Type
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     */
    public function getType(): Type
    {
        return $this->getOrFail('type');
    }
}
