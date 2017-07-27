<?php

namespace Cocktales\Domain\Profile\Hydration;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_profile_entity()
    {
        $profile = Hydrator::fromRawData((object) [
            'user_id' => (new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7'))->toBinary(),
            'username' => 'joe',
            'first_name' => 'Joe',
            'last_name' => 'Sweeny',
            'location' => 'Essex',
            'slogan' => 'Be drunk and happy',
            'avatar' => 'pic.jpg',
            'created_at' => '2017-03-12 00:00:00',
            'updated_at' => '2017-03-12 00:00:00'
        ]);

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $profile->getUserId()->__toString());
        $this->assertEquals('joe', $profile->getUsername());
        $this->assertEquals('Joe', $profile->getFirstName());
        $this->assertEquals('Sweeny', $profile->getLastName());
        $this->assertEquals('Essex', $profile->getLocation());
        $this->assertEquals('Be drunk and happy', $profile->getSlogan());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $profile->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $profile->getLastModifiedDate());
    }
}
