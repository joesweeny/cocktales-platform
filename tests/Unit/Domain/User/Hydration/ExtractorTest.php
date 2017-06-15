<?php

namespace Cocktales\Domain\User\Hydration;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_user_entity_into_raw_data()
    {
        $data = Extractor::toRawData(
            (new User)
                ->setId(new Uuid('ec0bff3c-3a9c-4f71-8a31-99936bd39f56'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('joe@example.com', $data->email);
        $this->assertEquals('2017-05-03 21:39:00', $data->created_at);
        $this->assertEquals('2017-05-03 21:39:00', $data->updated_at);
    }
}
