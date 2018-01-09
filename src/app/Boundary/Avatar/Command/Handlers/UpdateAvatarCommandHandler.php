<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;

class UpdateAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;

    /**
     * UpdateAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     */
    public function __construct(AvatarOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param UpdateAvatarCommand $command
     * @return void
     */
    public function handle(UpdateAvatarCommand $command): void
    {
        $this->orchestrator->updateAvatar($command->getUserId(), $command->getFileContents());
    }
}
