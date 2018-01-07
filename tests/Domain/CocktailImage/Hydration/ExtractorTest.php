<?php

namespace Cocktales\Domain\CocktailImage\Hydration;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_to_raw_data_extracts_a_cocktail_image_into_raw_data()
    {
        $data = Extractor::toRawData(
            (new CocktailImage(
                new Uuid('0deca1a4-9247-4660-a429-0337557ff8c9'),
                'filename.png'
            ))->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
        );

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('0deca1a4-9247-4660-a429-0337557ff8c9', Uuid::createFromBinary($data->cocktail_id));
        $this->assertEquals(1489276800, $data->created_at);
    }
}
