<?php

namespace Cocktales\Domain\Profile\Hydration;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_profile_entity_into_raw_data()
    {
        $data = Extractor::toRawData(
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
        $this->assertEquals('77c4aba1-9ee9-48ed-a5a1-c65b345d5249', Uuid::createFromBinary($data->id)->__toString());
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', Uuid::createFromBinary($data->user_id)->__toString());
        $this->assertEquals('joe', $data->username);
        $this->assertEquals('Joe', $data->first_name);
        $this->assertEquals('Sweeny', $data->last_name);
        $this->assertEquals('Essex', $data->location);
        $this->assertEquals('Oi Oi', $data->slogan);
        $this->assertEquals('pic.jpg', $data->avatar);
        $this->assertEquals('2017-03-12 00:00:00', $data->created_at);
        $this->assertEquals('2017-03-12 00:00:00', $data->updated_at);
    }
}
