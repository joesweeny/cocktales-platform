<?php

namespace Cocktales\Domain\CocktailIngredient\Hydration;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_cocktail_ingredient_entity_into_raw_data()
    {
        $data = Extractor::toRawData(new CocktailIngredient(
            new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'),
            new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
            1,
            50,
            'ml'
        ));

        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) Uuid::createFromBinary($data->cocktail_id));
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) Uuid::createFromBinary($data->ingredient_id));
        $this->assertEquals(1, $data->order_number);
        $this->assertEquals(50, $data->quantity);
        $this->assertEquals('ml', $data->measurement);
    }
}
