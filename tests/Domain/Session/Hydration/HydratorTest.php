<?php

namespace Cocktales\Domain\Session\Hydration;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_from_raw_data_converts_raw_data_into_session_token_object()
    {
        $token = Hydrator::fromRawData(
            (object) [
                'token' => (new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'))->toBinary(),
                'user_id' => (new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'))->toBinary(),
                'created_at' => 1513023602,
                'expiry' => 1513023602
            ]
        );

        $this->assertEquals('a4a93668-6e61-4a81-93b4-b2404dbe9788', $token->getToken());
        $this->assertEquals('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed', $token->getUserId());
        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $token->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $token->getExpiry());
    }
}
