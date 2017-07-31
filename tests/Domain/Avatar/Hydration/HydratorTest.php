<?php

namespace Cocktales\Domain\Avatar\Hydration;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_avatar_entity()
    {
        $avatar = Hydrator::fromRawData((object) [
            'user_id' => (new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))->toBinary(),
            'thumbnail' => 'thumbnail.jpg',
            'standard' => 'standard.jpg',
            'created_at' => 1489316160,
            'updated_at' => 1489316160
        ]);

        $this->assertInstanceOf(Avatar::class, $avatar);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $avatar->getUserId()->__toString());
        $this->assertEquals('thumbnail.jpg', $avatar->getThumbnail());
        $this->assertEquals('standard.jpg', $avatar->getStandard());
        $this->assertEquals('2017-03-12 10:56:00', $avatar->getCreatedDate());
        $this->assertEquals('2017-03-12 10:56:00', $avatar->getLastModifiedDate());
    }
}
