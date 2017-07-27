<?php

namespace Cocktales\Boundary\Profile\Command\Handlers;

use Cocktales\Boundary\Profile\Command\GetProfileByUserIdCommand;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Domain\Profile\ProfilePresenter;

class GetProfileByUserIdCommandHandler
{
    /**
     * @var ProfileOrchestrator
     */
    private $orchestrator;
    /**
     * @var ProfilePresenter
     */
    private $presenter;

    /**
     * GetProfileByUserIdCommandHandler constructor.
     * @param ProfileOrchestrator $orchestrator
     * @param ProfilePresenter $presenter
     */
    public function __construct(ProfileOrchestrator $orchestrator, ProfilePresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetProfileByUserIdCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws \Cocktales\Framework\Exception\UndefinedValueException
     * @throws \Cocktales\Framework\Exception\UndefinedException
     */
    public function handle(GetProfileByUserIdCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->orchestrator->getProfileByUserId($command->getUserId()));
    }
}
