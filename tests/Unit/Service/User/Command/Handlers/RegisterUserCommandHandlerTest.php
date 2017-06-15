<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\Validation\Validator;
use Cocktales\Service\User\Command\RegisterUserCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RegisterUserCommandHandlerTest extends TestCase
{
    public function test_handle_creates_a_new_user()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var Validator $validator */
        $validator = $this->prophesize(Validator::class);
        $handler = new RegisterUserCommandHandler($orchestrator->reveal(), $validator->reveal());

        $command = new RegisterUserCommand((object) [
            'email' => 'joe@example.com',
            'password' => 'password',
        ]);

        $validator->isEmailUnique($command->getEmail())->shouldBeCalled()->willReturn(true);

        $orchestrator->createUser(Argument::type(User::class))->shouldBeCalled();

        $handler->handle($command);
    }
}
