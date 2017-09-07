<?php

namespace Cocktales\Domain\CocktailIngredient;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class CocktailIngredientPresenterTest extends TestCase
{
    public function test_to_dto_returns_a_std_class_object_containing_ingredient_information()
    {
        $dto = (new CocktailIngredientPresenter)->toDto(
            new CocktailIngredient(
                new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
                new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
                1,
                50,
                'ml'
            ),
            (new Ingredient('73f261d9-234e-4501-a5dc-8f4f0bc0623a'))
                ->setName('Smirnoff Red')
        );

        $this->assertEquals('Smirnoff Red', $dto->name);
        $this->assertEquals(1, $dto->order_number);
        $this->assertEquals(50, $dto->quantity);
        $this->assertEquals('ml', $dto->measurement);
    }
}
