<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @var  Clock */
    private $clock;
    /** @var  Validator */
    private $validator;

    public function setUp()
    {
        $this->clock = new SystemClock();
        $this->validator = new Validator(
            $this->clock
        );
    }

    public function test_validate_returns_false_if_token_date_has_expired()
    {
        $token = new SessionToken(
            new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
            $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-11 20:20:02'),
            new \DateTimeImmutable('2017-12-11 20:20:02')
        );

        $this->assertFalse($this->validator->isValid($token));
    }

    public function test_validate_returns_true_if_token_date_has_not_expired_and_belongs_to_user()
    {
        $token = new SessionToken(
            new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
            $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-11 20:20:02'),
            new \DateTimeImmutable('2025-12-01 12:00:00')
        );

        $this->assertTrue($this->validator->isValid($token));
    }
}
