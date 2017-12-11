<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class ValidatorTest extends TestCase
{
    /** @var  TokenOrchestrator|ObjectProphecy */
    private $orchestrator;
    /** @var  Clock */
    private $clock;
    /** @var  Validator */
    private $validator;
    /** @var  LoggerInterface */
    private $logger;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(TokenOrchestrator::class);
        $this->clock = new SystemClock();
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->validator = new Validator(
            $this->orchestrator->reveal(),
            $this->clock,
            $this->logger->reveal()
        );
    }

    public function test_validate_returns_false_if_token_date_has_expired()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1513023602
            )
        );

        $this->assertFalse($this->validator->validate($token->getToken(), $id));
    }

    public function test_validate_returns_true_if_token_date_has_not_expired_and_belongs_to_user()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1764547200
            )
        );

        $this->assertTrue($this->validator->validate($token->getToken(), $id));
    }

    public function test_validate_returns_false_and_logs_an_error_if_token_does_not_belong_to_user()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1764547200
            )
        );

        $this->logger->error(
            'User dacbd63b-bf6f-443b-b64d-8110ea30204e has attempted to use Token ed542c55-3fa8-40a9-bbd0-4efa0a5a211a that does not belong to them'
        )->shouldBeCalled();

        $this->assertFalse(
            $this->validator->validate($token->getToken(), new Uuid('dacbd63b-bf6f-443b-b64d-8110ea30204e'))
        );
    }
}
