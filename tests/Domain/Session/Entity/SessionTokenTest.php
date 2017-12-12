<?php

namespace Cocktales\Domain\Session\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class SessionTokenTest extends TestCase
{
    public function test_properties_are_set_correctly_on_session_token_object()
    {
        $token = new SessionToken(
            new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-11 20:20:02'),
            new \DateTimeImmutable('2017-12-11 20:20:02')
        );

        $this->assertEquals('a4a93668-6e61-4a81-93b4-b2404dbe9788', $token->getToken());
        $this->assertEquals('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed', $token->getUserId());
        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $token->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $token->getExpiry());
    }
}
