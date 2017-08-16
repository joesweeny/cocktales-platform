<?php

namespace Cocktales\Domain\Ingredient\Entity;

use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\Exception\UndefinedValueException;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{
    public function test_setters_and_getters_on_ingredient_entity()
    {
        $ingredient = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA())
            ->setCreatedDate(new \DateTimeImmutable('2017-08-16 13:10:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-08-16 13:10:00'));

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $ingredient->getId()->__toString());
        $this->assertEquals('Smirnoff Red', $ingredient->getName());
        $this->assertEquals(Category::SPIRIT(), $ingredient->getCategory());
        $this->assertEquals(Type::VODKA(), $ingredient->getType());
        $this->assertEquals('2017-08-16 13:10:00', $ingredient->getCreatedDate());
        $this->assertEquals('2017-08-16 13:10:00', $ingredient->getLastModifiedDate());
    }

    public function test_exception_thrown_if_attempting_to_retrieve_a_value_that_has_not_been_set()
    {
        $this->expectException(UndefinedValueException::class);
        (new Ingredient)->getName();

        $this->expectException(UndefinedValueException::class);
        (new Ingredient)->getType();

        $this->expectException(UndefinedValueException::class);
        (new Ingredient)->getCategory();
    }
}
