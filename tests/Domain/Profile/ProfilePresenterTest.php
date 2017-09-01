<?php

namespace Cocktales\Domain\Profile;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ProfilePresenterTest extends TestCase
{
    public function test_convert_profile_entity_into_dto()
    {
        $data = (new ProfilePresenter)->toDto(
            (new Profile)
                ->setUserId(new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setLocation('Essex')
                ->setSlogan('Oi Oi')
                ->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-03-12 00:00:00'))
        );

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $data->user_id);
        $this->assertEquals('joe', $data->username);
        $this->assertEquals('Joe', $data->first_name);
        $this->assertEquals('Sweeny', $data->last_name);
        $this->assertEquals('Essex', $data->location);
        $this->assertEquals('Oi Oi', $data->slogan);
        $this->assertEquals('12/03/2017', $data->created_at);
        $this->assertEquals('12/03/2017', $data->updated_at);
    }
}
