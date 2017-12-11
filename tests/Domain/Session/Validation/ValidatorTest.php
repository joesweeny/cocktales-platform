<?php

namespace Cocktales\Domain\Session\Validation;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class ValidatorTest extends TestCase
{
    /** @var  TokenOrchestrator|ObjectProphecy */
    private $orchestrator;
    /** @var  Clock */
    private $clock;
    /** @var  Validator */
    private $validator;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(TokenOrchestrator::class);
        $this->clock = new SystemClock();
        $this->validator = new Validator(
            $this->orchestrator->reveal(),
            $this->clock
        );
    }

    public function test_has_token_expired_returns_true_if_token_date_has_expired()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1513023602
            )
        );

        $this->assertTrue($this->validator->hasTokenExpired($token->getToken()));
    }

    public function test_has_token_expired_returns_false_if_token_date_has_not_expired()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1764547200
            )
        );

        $this->assertFalse($this->validator->hasTokenExpired($token->getToken()));
    }

    public function test_token_belongs_to_user_returns_true_if_token_belongs_to_user()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                $id = new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1513023602
            )
        );

        $this->assertTrue($this->validator->tokenBelongsToUser($token->getToken(), $id));
    }

    public function test_token_belongs_to_user_returns_false_if_token_does_not_belong_to_user()
    {
        $this->orchestrator->getToken(new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'))->willReturn(
            $token = new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                1513023602,
                1513023602
            )
        );

        $this->assertFalse(
            $this->validator->tokenBelongsToUser($token->getToken(), new Uuid('dacbd63b-bf6f-443b-b64d-8110ea30204e'))
        );
    }
}
