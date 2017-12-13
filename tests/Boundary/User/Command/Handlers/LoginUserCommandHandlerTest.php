<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LoginUserCommand;
use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class LoginUserCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  LoginUserCommandHandler */
    private $handler;
    /** @var  TokenOrchestrator */
    private $tokens;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->tokens = $this->prophesize(TokenOrchestrator::class);
        $this->handler = new LoginUserCommandHandler(
            $this->orchestrator->reveal(),
            $this->tokens->reveal()
        );
    }

    public function test_handle_returns_token_string_if_user_credentials_are_correct()
    {
        $command = new LoginUserCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willReturn(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())->willReturn(true);

        $this->tokens->createToken($user->getId())->willReturn(
            new SessionToken(
                new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
                $user->getId(),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $this->assertEquals('a4a93668-6e61-4a81-93b4-b2404dbe9788', $this->handler->handle($command));
    }

    public function test_handle_throws_user_validation_exception_if_user_email_does_not_exist()
    {
        $command = new LoginUserCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willThrow(new NotFoundException());

        $this->expectException(UserValidationException::class);
        $this->handler->handle($command);
    }

    public function test_handle_throws_user_validation_exception_if_email_password_combination_does_not_match()
    {
        $command = new LoginUserCommand('joe@joe.com', 'password');

        $this->orchestrator->getUserByEmail($command->getEmail())->willReturn(
            $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('wrong-password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())->willReturn(false);

        $this->expectException(UserValidationException::class);
        $this->handler->handle($command);
    }
}
