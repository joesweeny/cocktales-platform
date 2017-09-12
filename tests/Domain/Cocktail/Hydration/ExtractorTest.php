<?php

namespace Cocktales\Domain\Cocktail\Hydration;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_cocktail_entity_into_raw_data()
    {
        $data = Extractor::toRawData((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed')->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00')));

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', (string) Uuid::createFromBinary($data->id));
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) Uuid::createFromBinary($data->user_id));
        $this->assertEquals('The Titty Twister', $data->name);
        $this->assertEquals('Made in my garage when pissed', $data->origin);
        $this->assertEquals(1489276800, $data->created_at);
    }
}
