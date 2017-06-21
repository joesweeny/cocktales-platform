<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Service\User\Command\GetUserByIdCommand;

class GetUserByIdCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * GetUserByIdCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GetUserByIdCommand $command
     * @return User
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetUserByIdCommand $command): User
    {
        return $this->orchestrator->getUserById($command->getId());
    }
}
