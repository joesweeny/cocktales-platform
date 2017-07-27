<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Boundary\User\Command\GetUserByIdCommand;
use PHPUnit\Framework\TestCase;

class GetUserByIdCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  GetUserByIdCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->handler = new GetUserByIdCommandHandler($this->orchestrator->reveal());
    }

    public function test_handle_returns_a_user_object_if_user_id_is_stored_in_the_database()
    {
        $command = new GetUserByIdCommand('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->orchestrator->getUserById($command->getId())->shouldBeCalled()->willReturn(
            new User('dc5b6421-d452-4862-b741-d43383c3fe1d')
        );

        $this->handler->handle($command);
    }

    public function test_handle_throws_not_found_exception_if_user_id_is_not_stored_in_database()
    {
        $command = new GetUserByIdCommand('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->orchestrator->getUserById($command->getId())->shouldBeCalled()->willThrow(NotFoundException::class);

        $this->expectException(NotFoundException::class);

        $this->handler->handle($command);
    }
}
