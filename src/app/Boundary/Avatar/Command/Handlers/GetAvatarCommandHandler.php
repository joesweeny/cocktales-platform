<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\AvatarPresenter;
use Cocktales\Boundary\Avatar\Command\GetAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;

class GetAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;
    /**
     * @var AvatarPresenter
     */
    private $presenter;

    /**
     * GetAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     * @param AvatarPresenter $presenter
     */
    public function __construct(AvatarOrchestrator $orchestrator, AvatarPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param GetAvatarCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetAvatarCommand $command): \stdClass
    {
        return $this->presenter->toDto(
            $this->orchestrator->getAvatarByUserId($command->getUserId())
        );
    }
}
