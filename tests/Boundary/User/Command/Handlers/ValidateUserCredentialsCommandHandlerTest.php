<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\ValidateUserCredentialsCommand;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Password\PasswordHash;
use PHPUnit\Framework\TestCase;

class ValidateUserCredentialsCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  ValidationUserCredentialsCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->handler = new ValidationUserCredentialsCommandHandler($this->orchestrator->reveal());
    }

    public function test_handle_returns_true_if_user_credentials_are_correct()
    {
        $command = new ValidateUserCredentialsCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willReturn(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())->willReturn(true);

        $this->assertTrue($this->handler->handle($command));
    }

    public function test_handle_returns_false_if_user_email_does_not_exist()
    {
        $command = new ValidateUserCredentialsCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willThrow(new NotFoundException());

        $this->assertFalse($this->handler->handle($command));
    }

    public function test_handle_returns_false_if_email_password_combination_does_not_match()
    {
        $command = new ValidateUserCredentialsCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willReturn(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('wrong-password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())->willReturn(false);

        $this->assertFalse($this->handler->handle($command));
    }
}
