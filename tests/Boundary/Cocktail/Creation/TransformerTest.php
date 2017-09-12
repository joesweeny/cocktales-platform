<?php

namespace Cocktales\Boundary\Cocktail\Creation;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    /** @var  Transformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new Transformer;
    }

    public function test_rawIngredientToObject_returns_an_ingredient_object_with_properties_set()
    {
        $ingredient = $this->transformer->rawIngredientToObject((object) [
            'cocktailId' => 'fe8f3ec8-1711-412c-8324-c1e1e5f19454',
            'ingredientId' => '73f261d9-234e-4501-a5dc-8f4f0bc0623a',
            'orderNumber' => 1,
            'quantity' => 50,
            'measurement' => 'ml'
        ]);

        $this->assertInstanceOf(CocktailIngredient::class, $ingredient);
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $ingredient->getCocktailId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $ingredient->getIngredientId());
        $this->assertEquals(1, $ingredient->getOrderNumber());
        $this->assertEquals(50, $ingredient->getQuantity());
        $this->assertEquals('ml', $ingredient->getMeasurement());
    }
}
