<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Boundary\User\Command\RegisterUserCommand;

class RegisterUserCommandHandler
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
     * RegisterUserCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param RegisterUserCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\UserEmailValidationException
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function handle(RegisterUserCommand $command): \stdClass
    {
        $user = $this->createUserEntity($command);

        if (!$this->orchestrator->canCreateNewUser($user)) {
            throw new UserEmailValidationException("A user has already registered with this email address {$user->getEmail()}");
        }

        return $this->presenter->toDto($this->orchestrator->createUser($user));
    }

    /**
     * @param RegisterUserCommand $command
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    private function createUserEntity(RegisterUserCommand $command): User
    {
        return (new User)
            ->setEmail($command->getEmail())
            ->setPasswordHash($command->getPassword());
    }
}
