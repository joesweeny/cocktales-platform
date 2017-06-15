<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
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

    public function handle(CreateUserCommand $command)
    {
        return $this->orchestrator->createUser(
            (new User)->setEmail($command->getEmail())->setPasswordHash($command->getPassword())
        );
    }
}
