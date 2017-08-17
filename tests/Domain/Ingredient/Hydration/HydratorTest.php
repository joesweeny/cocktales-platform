<?php

namespace Cocktales\Domain\Ingredient\Hydration;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_ingredient_entity()
    {
        $ingredient = Hydrator::fromRawData((object) [
            'id' => (new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7'))->toBinary(),
            'name' => "Gordon's Gin",
            'category' => 'Spirit',
            'type' => 'Gin',
            'created_at' => 1489316160,
            'updated_at' => 1489316160,
        ]);

        $this->assertInstanceOf(Ingredient::class, $ingredient);
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $ingredient->getId()->__toString());
        $this->assertEquals("Gordon's Gin", $ingredient->getName());
        $this->assertEquals(Category::SPIRIT(), $ingredient->getCategory());
        $this->assertEquals(Type::GIN(), $ingredient->getType());
        $this->assertEquals('2017-03-12 10:56:00', $ingredient->getCreatedDate());
        $this->assertEquals('2017-03-12 10:56:00', $ingredient->getLastModifiedDate());
    }
}
