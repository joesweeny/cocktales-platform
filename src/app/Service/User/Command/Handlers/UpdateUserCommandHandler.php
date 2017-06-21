<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\UserEmailValidation;
use Cocktales\Framework\Exception\UserPasswordValidation;
use Cocktales\Service\User\Command\UpdateUserCommand;

class UpdateUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * UpdateUserCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param UpdateUserCommand $command
     * @return \Cocktales\Domain\User\Entity\User
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\UserPasswordValidation
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws UserEmailValidation
     */
    public function handle(UpdateUserCommand $command): User
    {
        // Throws Not Found Exception
        $user = $this->orchestrator->getUserById($command->getUserId());

        if ($user->getEmail() != $command->getEmail()) {
            if (!$this->orchestrator->canUpdateUser($command->getEmail())) {
                throw new UserEmailValidation("A user has already registered with this email address {$user->getEmail()}");
            }

            $user->setEmail($command->getEmail());
        }

        if ($command->getNewPassword()) {
            if (!$this->orchestrator->validateUserPassword($command->getUserId(), $command->getOldPassword())) {
                throw new UserPasswordValidation('Password does not match the password stored for this user');
            }

            $user->setPasswordHash($command->getNewPassword());
        }

        return $this->orchestrator->updateUser($user);
    }
}
