<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;

class CreateAvatarCommandHandler
{
    /**
     * @var AvatarOrchestrator
     */
    private $orchestrator;

    /**
     * CreateAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     */
    public function __construct(AvatarOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param CreateAvatarCommand $command
     * @return void
     */
    public function handle(CreateAvatarCommand $command): void
    {
        $this->orchestrator->createAvatar($command->getUserId(), $command->getFileContents());
    }
}
