<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\FixedClock;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class TokenRefresherTest extends TestCase
{
    /** @var  TokenRefresher */
    private $refresher;

    public function setUp()
    {
        $clock = new FixedClock(new \DateTimeImmutable('2017-12-12 12:30:00'));
        $this->refresher = new TokenRefresher($clock);
    }

    public function test_expires_within_hour_returns_true_if_token_expiry_is_less_than_one_hour_and_greater_than_zero()
    {
        $token = new SessionToken(
            new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-01 12:00:00'),
            new \DateTimeImmutable('2017-12-12 13:00:00')
        );

        $this->assertTrue($this->refresher->expiresWithinHour($token->getExpiry()));
    }

    public function test_expires_within_hour_returns_false_if_token_expiry_is_more_than_one_hour_or_expired()
    {
        $token1 = new SessionToken(
            new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-01 12:00:00'),
            new \DateTimeImmutable('2017-12-12 12:00:00')
        );

        $token2 = new SessionToken(
            new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-01 12:00:00'),
            new \DateTimeImmutable('2017-12-15 12:00:00')
        );

        $this->assertFalse($this->refresher->expiresWithinHour($token1->getExpiry()));
        $this->assertFalse($this->refresher->expiresWithinHour($token2->getExpiry()));
    }

    public function test_refresh_to_hour_sets_token_expiry_to_an_hour_from_when_its_refreshed()
    {
        $token = new SessionToken(
            new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-01 12:00:00'),
            new \DateTimeImmutable('2017-12-12 13:00:00')
        );

        $token = $this->refresher->refreshToHour($token);

        $this->assertEquals(new \DateTimeImmutable('2017-12-12 13:30:00'), $token->getExpiry());
    }
}
