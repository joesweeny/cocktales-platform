<?php

namespace Cocktales\Domain\Ingredient\Hydration;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_ingredient_entity_into_raw_data()
    {
        $data = Extractor::toRawData((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA())
            ->setCreatedDate(new \DateTimeImmutable('2017-03-12 10:56:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-03-12 10:56:00')));

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', Uuid::createFromBinary($data->id)->__toString());
        $this->assertEquals('Spirit', $data->category);
        $this->assertEquals('Vodka', $data->type);
        $this->assertEquals(1489316160, $data->created_at);
        $this->assertEquals(1489316160, $data->updated_at);
    }
}
