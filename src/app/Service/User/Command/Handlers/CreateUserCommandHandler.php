<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Service\User\Command\CreateUserCommand;

class CreateUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * CreateUserCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param CreateUserCommand $command
     * @return User
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\UserEmailValidationException
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function handle(CreateUserCommand $command): User
    {
        $user = $this->createUserEntity($command);

        if (!$this->orchestrator->canCreateNewUser($user)) {
            throw new UserEmailValidationException("A user has already registered with this email address {$user->getEmail()}");
        }

        return $this->orchestrator->createUser($user);
    }

    /**
     * @param CreateUserCommand $command
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    private function createUserEntity(CreateUserCommand $command): User
    {
        return (new User)
            ->setEmail($command->getEmail())
            ->setPasswordHash($command->getPassword());
    }
}
