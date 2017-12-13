<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\Validation\Validator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class SessionManagerTest extends TestCase
{
    /** @var  TokenOrchestrator|ObjectProphecy */
    private $orchestrator;
    /** @var  Validator|ObjectProphecy */
    private $validator;
    /** @var  TokenRefresher|ObjectProphecy */
    private $refresher;
    /** @var  SessionManager */
    private $manager;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(TokenOrchestrator::class);
        $this->validator = $this->prophesize(Validator::class);
        $this->refresher = $this->prophesize(TokenRefresher::class);
        $this->manager = new SessionManager(
            $this->orchestrator->reveal(),
            $this->validator->reveal(),
            $this->refresher->reveal()
        );
    }

    public function test_handle_token_checks_token_validation_update_token_expiry_and_updates_token_by_saving_to_database()
    {
        $this->orchestrator->getToken(
            $tokenId = new Uuid('d3531cef-794e-4333-b925-b45b80b8f591')
        )->willReturn($token = new SessionToken(
            $tokenId,
            $userId = new Uuid('d745e7e1-331a-433b-a58a-63aea4271653'),
            new \DateTimeImmutable(),
            (new \DateTimeImmutable)->add(new \DateInterval('PT30M'))
        ));

        $this->validator->validate($token, $userId)->willReturn(true);

        $this->refresher->expiresWithinHour($token->getExpiry())->willReturn(true);

        $this->refresher->refreshToHour($token)->willReturn($token);

        $this->orchestrator->updateToken($token)->shouldBeCalled();

        $this->manager->handleToken($tokenId, $userId);
    }

    public function test_handle_token_throws_an_exception_if_token_provided_is_not_valid()
    {
        $this->orchestrator->getToken(
            $tokenId = new Uuid('d3531cef-794e-4333-b925-b45b80b8f591')
        )->willReturn($token = new SessionToken(
            $tokenId,
            $userId = new Uuid('d745e7e1-331a-433b-a58a-63aea4271653'),
            new \DateTimeImmutable(),
            (new \DateTimeImmutable)->sub(new \DateInterval('P1D'))
        ));

        $this->validator->validate($token, $userId)->willReturn(false);

        $this->refresher->expiresWithinHour(Argument::any())->shouldNotBeCalled();

        $this->refresher->refreshToHour(Argument::any())->shouldNotBeCalled();

        $this->orchestrator->updateToken(Argument::any())->shouldNotBeCalled();

        $this->expectException(SessionTokenValidationException::class);
        $this->manager->handleToken($tokenId, $userId);
    }

    public function test_token_is_not_updated_if_token_does_not_expires_within_an_hour()
    {
        $this->orchestrator->getToken(
            $tokenId = new Uuid('d3531cef-794e-4333-b925-b45b80b8f591')
        )->willReturn($token = new SessionToken(
            $tokenId,
            $userId = new Uuid('d745e7e1-331a-433b-a58a-63aea4271653'),
            new \DateTimeImmutable(),
            (new \DateTimeImmutable)->add(new \DateInterval('P1D'))
        ));

        $this->validator->validate($token, $userId)->willReturn(true);

        $this->refresher->expiresWithinHour($token->getExpiry())->willReturn(false);

        $this->refresher->refreshToHour(Argument::any())->shouldNotBeCalled();

        $this->orchestrator->updateToken(Argument::any())->shouldNotBeCalled();

        $this->manager->handleToken($tokenId, $userId);
    }

    public function test_exception_is_thrown_if_token_is_not_found()
    {
        $this->orchestrator->getToken(
            $tokenId = new Uuid('d3531cef-794e-4333-b925-b45b80b8f591')
        )->willThrow(new NotFoundException());

        $this->validator->validate(Argument::any())->shouldNotBeCalled();

        $this->refresher->expiresWithinHour(Argument::any())->shouldNotBeCalled();

        $this->refresher->refreshToHour(Argument::any())->shouldNotBeCalled();

        $this->orchestrator->updateToken(Argument::any())->shouldNotBeCalled();

        $this->expectException(SessionTokenValidationException::class);
        $this->manager->handleToken($tokenId, new Uuid('d745e7e1-331a-433b-a58a-63aea4271653'));

    }
}
