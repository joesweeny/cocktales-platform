<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
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
     * @return mixed
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function handle(CreateUserCommand $command)
    {
        try {
            $this->orchestrator->getUserByEmail($command->getEmail());

        } catch (NotFoundException $e) {

            return $this->orchestrator->createUser($this->createUserEntity($command));
        }
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
