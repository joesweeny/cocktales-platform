<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
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
     * @return User
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(UpdateUserCommand $command): User
    {
        $user = $this->orchestrator->getUserById(new Uuid($command->getId()));

        return $this->orchestrator->updateUser(
            $user->setUsername($command->getUsername())
        );
    }
}
