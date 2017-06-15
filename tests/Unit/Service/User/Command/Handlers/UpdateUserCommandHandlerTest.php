<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\User\Command\UpdateUserCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class UpdateUserCommandHandlerTest extends TestCase
{
    public function test_handle_updates_a_user_record_in_the_database()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        $handler = new UpdateUserCommandHandler($orchestrator->reveal());

        $command = new UpdateUserCommand((object) [
            'id' => '2bb63d47-85ad-4197-8947-279861282388',
            'username' => 'joe',
            'email' => 'joe@example.com',
        ]);

        $orchestrator->getUserById($command->getId())->shouldBeCalled()->willReturn(
            $user = (new User)->setId(new Uuid('2bb63d47-85ad-4197-8947-279861282388'))->setUsername('bob')->setEmail('joe@example.com')
        );

        $orchestrator->updateUser(Argument::that(function (User $user) {
            $this->assertEquals('joe', $user->getUsername());
            $this->assertEquals('joe@example.com', $user->getEmail());
            return true;
        }))->shouldBeCalled();

        $handler->handle($command);
    }
}
