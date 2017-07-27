<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Boundary\User\Command\GetUserByIdCommand;
use Cocktales\Domain\User\UserPresenter;

class GetUserByIdCommandHandler
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
     * GetUserByIdCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetUserByIdCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetUserByIdCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getUserById($command->getId()));
    }
}
