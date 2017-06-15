<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Service\User\Command\CreateUserCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateUserCommandHandlerTest extends TestCase
{
    public function test_handle_create_a_new_user_record_in_the_database()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        $handler = new CreateUserCommandHandler($orchestrator->reveal());

        $command = new CreateUserCommand((object) [
            'email' => 'joe@email.com',
            'password' => 'password'
        ]);

        $orchestrator->getUserByEmail($command->getEmail())->shouldBeCalled()->willThrow(NotFoundException::class);

        $orchestrator->createUser(Argument::that(function (User $user) {
            $this->assertEquals('joe@email.com', $user->getEmail());
            return true;
        }))->shouldBeCalled();

        $handler->handle($command);
    }

    public function test_handle_does_not_create_a_new_user_record_in_the_database_if_user_with_email_already_exists()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        $handler = new CreateUserCommandHandler($orchestrator->reveal());

        $command = new CreateUserCommand((object) [
            'email' => 'joe@email.com',
            'password' => 'password'
        ]);

        $orchestrator->getUserByEmail($command->getEmail())->shouldBeCalled()->willReturn((new User)->setEmail('joe@email.com'));

        $orchestrator->createUser(Argument::type(User::class))->shouldNotBeCalled();

        $handler->handle($command);
    }
}
