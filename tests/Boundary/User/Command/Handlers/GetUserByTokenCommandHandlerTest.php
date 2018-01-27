<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\GetUserByTokenCommand;
use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class GetUserByTokenCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator|ObjectProphecy */
    private $orchestrator;
    /** @var  TokenOrchestrator|ObjectProphecy */
    private $tokenOrchestrator;
    /** @var  GetUserByTokenCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->tokenOrchestrator = $this->prophesize(TokenOrchestrator::class);
        $this->handler = new GetUserByTokenCommandHandler(
            $this->orchestrator->reveal(),
            $this->tokenOrchestrator->reveal(),
            new UserPresenter
        );
    }

    public function test_handle_returns_a_scalar_object_containing_user_information()
    {
        $command = new GetUserByTokenCommand('5d2c1c46-c5a1-4ae9-84cf-4483f001086b');

        $this->tokenOrchestrator->getToken($command->getToken())->willReturn(
            $token = new SessionToken(
                new Uuid('5d2c1c46-c5a1-4ae9-84cf-4483f001086b'),
                $userId = new Uuid('bbf0eb3d-1a9a-4d9d-87e1-4c94178354e2'),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        );

        $this->orchestrator->getUserByToken($token)->willReturn(
            (new User($userId))
                ->setEmail('joe@email.com')
                ->setCreatedDate(new \DateTimeImmutable())
                ->setLastModifiedDate(new \DateTimeImmutable())
        );

        $data = $this->handler->handle($command);

        $this->assertEquals('bbf0eb3d-1a9a-4d9d-87e1-4c94178354e2', $data->id);
        $this->assertEquals('joe@email.com', $data->email);
    }

    public function test_handle_throws_not_found_exception_if_token_provided_does_not_exist()
    {
        $command = new GetUserByTokenCommand('5d2c1c46-c5a1-4ae9-84cf-4483f001086b');

        $this->tokenOrchestrator->getToken($command->getToken())->willThrow(NotFoundException::class);

        $this->orchestrator->getUserByToken(Argument::type(SessionToken::class))->shouldNotBeCalled();

        $this->expectException(NotFoundException::class);

        $this->handler->handle($command);
    }

    public function test_handle_throws_not_found_exception_if_user_does_not_exist()
    {
        $command = new GetUserByTokenCommand('5d2c1c46-c5a1-4ae9-84cf-4483f001086b');

        $this->tokenOrchestrator->getToken($command->getToken())->willReturn(
            $token = new SessionToken(
                new Uuid('5d2c1c46-c5a1-4ae9-84cf-4483f001086b'),
                $userId = new Uuid('bbf0eb3d-1a9a-4d9d-87e1-4c94178354e2'),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        );

        $this->orchestrator->getUserByToken($token)->willThrow(NotFoundException::class);

        $this->expectException(NotFoundException::class);

        $this->handler->handle($command);
    }
}
