<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Service\User\Command\GetUserByEmailCommand;
use PHPUnit\Framework\TestCase;

class GetUserByEmailCommandHandlerTest extends TestCase
{
    public function test_handle_retrieves_a_user_from_the_database_by_email()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        $handler = new GetUserByEmailCommandHandler($orchestrator->reveal());

        $command = new GetUserByEmailCommand('joe@example.com');

        $orchestrator->getUserByEmail($command->getEmail())->shouldBeCalled()->willReturn((new User)->setEmail($command->getEmail()));

        $handler->handle($command);
    }
}
