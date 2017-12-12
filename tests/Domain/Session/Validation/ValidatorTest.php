<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ValidatorTest extends TestCase
{
    /** @var  Clock */
    private $clock;
    /** @var  Validator */
    private $validator;
    /** @var  LoggerInterface */
    private $logger;

    public function setUp()
    {
        $this->clock = new SystemClock();
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->validator = new Validator(
            $this->clock,
            $this->logger->reveal()
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

        $this->assertFalse($this->validator->validate($token, $id));
    }

    public function test_validate_returns_true_if_token_date_has_not_expired_and_belongs_to_user()
    {
        $token = new SessionToken(
            new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
            $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-11 20:20:02'),
            new \DateTimeImmutable('2025-12-01 12:00:00')
        );

        $this->assertTrue($this->validator->validate($token, $id));
    }

    public function test_validate_returns_false_and_logs_an_error_if_token_does_not_belong_to_user()
    {
        $token = new SessionToken(
            new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
            new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
            new \DateTimeImmutable('2017-12-11 20:20:02'),
            new \DateTimeImmutable('2025-12-01 12:00:00')
        );

        $this->logger->error(
            'User dacbd63b-bf6f-443b-b64d-8110ea30204e has attempted to use Token ed542c55-3fa8-40a9-bbd0-4efa0a5a211a that does not belong to them'
        )->shouldBeCalled();

        $this->assertFalse(
            $this->validator->validate($token, new Uuid('dacbd63b-bf6f-443b-b64d-8110ea30204e'))
        );
    }
}
