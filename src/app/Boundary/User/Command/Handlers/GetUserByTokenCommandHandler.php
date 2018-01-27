<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\GetUserByTokenCommand;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;

class GetUserByTokenCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var TokenOrchestrator
     */
    private $tokenOrchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    public function __construct(
        UserOrchestrator $orchestrator,
        TokenOrchestrator $tokenOrchestrator,
        UserPresenter $presenter
    ) {
        $this->orchestrator = $orchestrator;
        $this->tokenOrchestrator = $tokenOrchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetUserByTokenCommand $command
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @return \stdClass
     */
    public function handle(GetUserByTokenCommand $command): \stdClass
    {
        $token = $this->tokenOrchestrator->getToken($command->getToken());

        return $this->presenter->toDto(
            $this->orchestrator->getUserByToken($token)
        );
    }
}
