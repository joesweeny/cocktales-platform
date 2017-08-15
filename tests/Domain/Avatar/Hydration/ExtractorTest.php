<?php

namespace Cocktales\Domain\Avatar\Hydration;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_avatar_entity_into_scalar_type()
    {
        $data = Extractor::toRawData((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg')
            ->setCreatedDate(new \DateTimeImmutable('2017-03-12 10:56:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-03-12 10:56:00')));

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', Uuid::createFromBinary($data->user_id)->__toString());
        $this->assertEquals('filename.jpg', $data->filename);
        $this->assertEquals(1489316160, $data->created_at);
        $this->assertEquals(1489316160, $data->updated_at);
    }
}
