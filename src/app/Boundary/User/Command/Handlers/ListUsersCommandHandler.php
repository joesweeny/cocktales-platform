<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\ListUsersCommand;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;

class ListUsersCommandHandler
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
     * ListUsersCommandHandler constructor.
     * @param UserOrchestrator $orchestrator
     * @param UserPresenter $presenter
     */
    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param ListUsersCommand $command
     * @return array
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function handle(ListUsersCommand $command): array
    {
        return array_map(function (User $user) {
            return $this->presenter->toDto($user);
        }, $this->orchestrator->getUsers()->toArray());
    }
}
