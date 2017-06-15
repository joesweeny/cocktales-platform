<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\User\Command\GetUserByIdCommand;
use PHPUnit\Framework\TestCase;

class GetUserByIdCommandHandlerTest extends TestCase
{
    public function test_handle_retrieves_a_user_from_the_database_by_id()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        $handler = new GetUserByIdCommandHandler($orchestrator->reveal());

        $command = new GetUserByIdCommand(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'));

        $orchestrator->getUserById($command->getUserId())->shouldBeCalled()->willReturn(
            (new User)->setId(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'))
        );

        $handler->handle($command);
    }
}
