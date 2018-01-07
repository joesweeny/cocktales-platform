<?php

namespace Cocktales\Domain\CocktailImage\Hydration;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_from_raw_data_converts_raw_data_into_a_cocktail_image_object()
    {
        $image = Hydrator::fromRawData((object) [
                'cocktail_id' => (new Uuid('0deca1a4-9247-4660-a429-0337557ff8c9'))->toBinary(),
                'filename' => 'filename.png',
                'created_at' => 1489276800
            ]
        );

        $this->assertInstanceOf(CocktailImage::class, $image);
        $this->assertEquals('0deca1a4-9247-4660-a429-0337557ff8c9', $image->getCocktailId());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $image->getCreatedDate());
    }
}
