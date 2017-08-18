<?php

namespace Cocktales\Boundary\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use PHPUnit\Framework\TestCase;

class IngredientPresenterTest extends TestCase
{
    public function test_presenter_returns_a_scalar_object_containing_ingredient_information()
    {
        $data = (new IngredientPresenter)->toDto((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA())
            ->setCreatedDate(new \DateTimeImmutable('2017-08-16 13:10:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-08-16 13:10:00')));

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data->id);
        $this->assertEquals('Smirnoff Red', $data->name);
        $this->assertEquals('Spirit', $data->category);
        $this->assertEquals('Vodka', $data->type);
    }
}
