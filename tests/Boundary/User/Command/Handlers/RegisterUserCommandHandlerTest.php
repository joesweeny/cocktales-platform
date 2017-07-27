<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\RegisterUserCommand;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\UserEmailValidationException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RegisterUserCommandHandlerTest extends TestCase
{
    public function test_handle_create_a_new_user_record_in_the_database()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var UserPresenter $presenter */
        $presenter = $this->prophesize(UserPresenter::class);
        $handler = new RegisterUserCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $command = new RegisterUserCommand((object) [
            'email' => 'joe@email.com',
            'password' => 'password'
        ]);

        $orchestrator->canCreateNewUser(Argument::type(User::class))->shouldBeCalled()->willReturn(true);

        $orchestrator->createUser(Argument::that(function (User $user) {
            $this->assertEquals('joe@email.com', $user->getEmail());
            return true;
        }))->shouldBeCalled();

        $presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $handler->handle($command);
    }

    public function test_handle_does_not_create_a_new_user_record_in_the_database_if_user_with_email_already_exists()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var UserPresenter $presenter */
        $presenter = $this->prophesize(UserPresenter::class);
        $handler = new RegisterUserCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $command = new RegisterUserCommand((object) [
            'email' => 'joe@email.com',
            'password' => 'password'
        ]);

        $orchestrator->canCreateNewUser(Argument::type(User::class))->shouldBeCalled()->willReturn(false);

        $orchestrator->createUser(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(UserEmailValidationException::class);
        $this->expectExceptionMessage('A user has already registered with this email address joe@email.com');

        $handler->handle($command);
    }
}
