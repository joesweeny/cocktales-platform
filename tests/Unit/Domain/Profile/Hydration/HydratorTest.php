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
            'id' => (new Uuid('77c4aba1-9ee9-48ed-a5a1-c65b345d5249'))->toBinary(),
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
        $this->assertEquals('77c4aba1-9ee9-48ed-a5a1-c65b345d5249', $profile->getId()->__toString());
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $profile->getUserId()->__toString());
        $this->assertEquals('joe', $profile->getUsername());
        $this->assertEquals('Joe', $profile->getFirstName());
        $this->assertEquals('Sweeny', $profile->getLastName());
        $this->assertEquals('Essex', $profile->getLocation());
        $this->assertEquals('Be drunk and happy', $profile->getSlogan());
        $this->assertEquals('pic.jpg', $profile->getAvatar());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $profile->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $profile->getLastModifiedDate());
    }

    public function test_converts_profile_entity_into_public_viewable_data()
    {
        $data = Hydrator::toPublicViewableData(
            (new Profile)
                ->setId(new Uuid('77c4aba1-9ee9-48ed-a5a1-c65b345d5249'))
                ->setUserId(new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setLocation('Essex')
                ->setSlogan('Oi Oi')
                ->setAvatar('pic.jpg')
                ->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
        );

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('77c4aba1-9ee9-48ed-a5a1-c65b345d5249', $data->id);
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $data->user_id);
        $this->assertEquals('joe', $data->username);
        $this->assertEquals('Joe', $data->first_name);
        $this->assertEquals('Sweeny', $data->last_name);
        $this->assertEquals('Essex', $data->location);
        $this->assertEquals('Oi Oi', $data->slogan);
        $this->assertEquals('pic.jpg', $data->avatar);
        $this->assertEquals('12/03/2017', $data->created_at);
        $this->assertEquals('12/03/2017', $data->updated_at);
    }
}
