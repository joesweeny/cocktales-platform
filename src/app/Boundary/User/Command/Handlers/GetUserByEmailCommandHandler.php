<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\GetUserByEmailCommand;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;

class GetUserByEmailCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetUserByEmailCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetUserByEmailCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getUserByEmail($command->getEmail()));
    }
}
