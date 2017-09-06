<?php

namespace Cocktales\Domain\CocktailIngredient\Hydration;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_cocktail_ingredient_entity()
    {
        $ingredient = Hydrator::fromRawData((object) [
            'cocktail_id' => (new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'))->toBinary(),
            'ingredient_id' => (new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'))->toBinary(),
            'order_number' => 5,
            'quantity' => 200,
            'measurement' => 'ml'
        ]);

        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $ingredient->getCocktailId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $ingredient->getIngredientId());
        $this->assertEquals(5, $ingredient->getOrderNumber());
        $this->assertEquals(200, $ingredient->getQuantity());
        $this->assertEquals('ml', $ingredient->getMeasurement());
    }
}
