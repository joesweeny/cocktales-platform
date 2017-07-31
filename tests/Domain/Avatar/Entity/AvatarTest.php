<?php

namespace Cocktales\Domain\Avatar\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class AvatarTest extends TestCase
{
    public function test_setter_and_getter_on_avatar_entity()
    {
        $avatar = (new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setThumbnail('thumbnail.jpg')
            ->setStandard('standard.jpg')
            ->setCreatedDate(new \DateTimeImmutable('2017-03-12 10:56:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-03-12 10:56:00'));

        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $avatar->getUserId()->__toString());
        $this->assertEquals('thumbnail.jpg', $avatar->getThumbnail());
        $this->assertEquals('standard.jpg', $avatar->getStandard());
        $this->assertEquals('2017-03-12 10:56:00', $avatar->getCreatedDate());
        $this->assertEquals('2017-03-12 10:56:00', $avatar->getLastModifiedDate());
    }
}
