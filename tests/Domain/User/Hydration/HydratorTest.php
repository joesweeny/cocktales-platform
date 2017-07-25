<?php

namespace Cocktales\Domain\User\Hydration;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_user_entity()
    {
        $user = Hydrator::fromRawData((object) [
            'id' => (new Uuid('ec0bff3c-3a9c-4f71-8a31-99936bd39f56'))->toBinary(),
            'email' => 'joe@example.com',
            'password' => 'password',
            'created_at' => '2017-05-03 21:39:00',
            'updated_at' => '2017-05-03 21:39:00'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('ec0bff3c-3a9c-4f71-8a31-99936bd39f56', $user->getId()->__toString());
        $this->assertEquals('joe@example.com', $user->getEmail());
        $this->assertEquals('2017-05-03 21:39:00', $user->getCreatedDate());
        $this->assertEquals('2017-05-03 21:39:00', $user->getLastModifiedDate());
    }
}