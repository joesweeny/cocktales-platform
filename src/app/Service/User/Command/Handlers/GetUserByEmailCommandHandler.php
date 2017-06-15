<?php

namespace Cocktales\Service\User\Command\Handlers;

use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Service\User\Command\GetUserByEmailCommand;

class GetUserByEmailCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * GetUserByEmailCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function handle(GetUserByEmailCommand $command)
    {
        return $this->orchestrator->getUserByEmail($command->getEmail());
    }
}
