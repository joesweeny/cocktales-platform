<?php

namespace Cocktales\Domain\CocktailIngredient\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class CocktailIngredientTest extends TestCase
{
    public function test_properties_are_set_on_cocktail_ingredient_entity()
    {
        $object = new CocktailIngredient(
            new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
            new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
            1,
            50,
            'ml'
        );

        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $object->getCocktailId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $object->getIngredientId());
        $this->assertEquals(1, $object->getOrderNumber());
        $this->assertEquals(50, $object->getQuantity());
        $this->assertEquals('ml', $object->getMeasurement());
    }
}
