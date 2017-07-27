<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Framework\Exception\UserPasswordValidationException;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Boundary\User\Command\UpdateUserCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class UpdateUserCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  UpdateUserCommandHandler */
    private $handler;
    /** @var  UserPresenter */
    private $presenter;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->presenter = $this->prophesize(UserPresenter::class);
        $this->handler = new UpdateUserCommandHandler($this->orchestrator->reveal(), $this->presenter->reveal());
    }

    public function test_handle_updates_a_user_email_record_successfully()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@newemail.com',
            'oldPassword' => '',
            'newPassword' => ''
        ]);

        $this->orchestrator->getUserById(new Uuid('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->shouldBeCalled()->willReturn(
            $user = (new User('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->setEmail('joe@email.com')
        );

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldNotBeCalled();

        $this->orchestrator->updateUser(Argument::that(function (User $user) {
            $this->assertEquals('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1', $user->getId()->__toString());
            $this->assertEquals('joe@newemail.com', $user->getEmail());
            return true;
        }))->shouldBeCalled();

        $this->presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_updates_a_user_password_successfully()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@email.com',
            'oldPassword' => 'password',
            'newPassword' => 'newPassword'
        ]);

        $this->orchestrator->getUserById(new Uuid('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->shouldBeCalled()->willReturn(
            $user = (new User('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))
                ->setEmail('joe@email.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldNotBeCalled();

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->updateUser(Argument::that(function (User $user) {
            $this->assertEquals('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1', $user->getId()->__toString());
            $this->assertEquals('joe@email.com', $user->getEmail());
            $this->assertTrue($user->getPasswordHash()->verify('newPassword'));
            return true;
        }))->shouldBeCalled();

        $this->presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_updates_both_email_and_password()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@newEmail.com',
            'oldPassword' => 'password',
            'newPassword' => 'newPassword'
        ]);

        $this->orchestrator->getUserById(new Uuid('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->shouldBeCalled()->willReturn(
            $user = (new User('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))
                ->setEmail('joe@email.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->updateUser(Argument::that(function (User $user) {
            $this->assertEquals('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1', $user->getId()->__toString());
            $this->assertEquals('joe@newEmail.com', $user->getEmail());
            $this->assertTrue($user->getPasswordHash()->verify('newPassword'));
            return true;
        }))->shouldBeCalled();

        $this->presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_exception_is_thrown_if_user_cannot_be_retrieved_by_id()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@newEmail.com',
            'oldPassword' => 'password',
            'newPassword' => 'newPassword'
        ]);

        $this->orchestrator->getUserById($command->getUserId())->shouldBeCalled()->willThrow(NotFoundException::class);

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldNotBeCalled();

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldNotBeCalled();

        $this->orchestrator->updateUser(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(NotFoundException::class);

        $this->handler->handle($command);
    }

    public function test_exception_is_thrown_if_updated_email_is_already_used_by_another_user()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@newEmail.com',
            'oldPassword' => 'password',
            'newPassword' => 'newPassword'
        ]);

        $this->orchestrator->getUserById(new Uuid('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->shouldBeCalled()->willReturn(
            $user = (new User('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))
                ->setEmail('joe@email.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldBeCalled()->willReturn(false);

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldNotBeCalled();

        $this->orchestrator->updateUser(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(UserEmailValidationException::class);

        $this->handler->handle($command);
    }

    public function test_exception_is_thrown_if_old_password_does_not_match_the_password_stored_against_user()
    {
        $command = new UpdateUserCommand((object) [
            'id' => '7aede48c-cf27-4c3e-90f9-c0c71a2c46e1',
            'email' => 'joe@newEmail.com',
            'oldPassword' => 'password',
            'newPassword' => 'newPassword'
        ]);

        $this->orchestrator->getUserById(new Uuid('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))->shouldBeCalled()->willReturn(
            $user = (new User('7aede48c-cf27-4c3e-90f9-c0c71a2c46e1'))
                ->setEmail('joe@email.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->orchestrator->canUpdateUser($command->getEmail())->shouldBeCalled()->willReturn(true);

        $this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())->shouldBeCalled()->willReturn(false);

        $this->orchestrator->updateUser(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(UserPasswordValidationException::class);

        $this->handler->handle($command);
    }
}
