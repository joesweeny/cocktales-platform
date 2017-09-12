<?php

namespace Cocktales\Domain\Cocktail\Hydration;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_cocktail_entity()
    {
        $cocktail = Hydrator::fromRawData((object) [
            'id' => (new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'))->toBinary(),
            'user_id' => (new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))->toBinary(),
            'name' => 'Woo Woo',
            'origin' => 'Cult classic',
            'created_at' => 1489276800
        ]);

        $this->assertInstanceOf(Cocktail::class, $cocktail);
        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) $cocktail->getId());
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $cocktail->getUserId());
        $this->assertEquals('Woo Woo', $cocktail->getName());
        $this->assertEquals('Cult classic', $cocktail->getOrigin());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $cocktail->getCreatedDate());
    }
}
