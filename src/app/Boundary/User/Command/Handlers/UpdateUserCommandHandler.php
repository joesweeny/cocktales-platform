<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Framework\Exception\UserPasswordValidationException;
use Cocktales\Boundary\User\Command\UpdateUserCommand;
use Cocktales\Framework\Password\PasswordHash;

class UpdateUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    /**
     * UpdateUserCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param UpdateUserCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UserEmailValidationException
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\UserPasswordValidationException
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(UpdateUserCommand $command): \stdClass
    {
        // Throws Not Found Exception
        $user = $this->orchestrator->getUserById($command->getUserId());

        if ($user->getEmail() != $command->getEmail()) {
            if (!$this->orchestrator->canUpdateUser($command->getEmail())) {
                throw new UserEmailValidationException("A user has already registered with this email address {$user->getEmail()}");
            }

            $user->setEmail($command->getEmail());
        }

        if ($command->getNewPassword()) {
            if (!$this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())) {
                throw new UserPasswordValidationException('Password does not match the password stored for this user');
            }

            $user->setPasswordHash(PasswordHash::createFromRaw($command->getNewPassword()));
        }

        return $this->presenter->toDto($this->orchestrator->updateUser($user));
    }
}
