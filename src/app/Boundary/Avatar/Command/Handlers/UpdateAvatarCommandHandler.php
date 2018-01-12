<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;

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
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @return void
     */
    public function handle(UpdateAvatarCommand $command): void
    {
        if (!$this->orchestrator->avatarExists($command->getUserId())) {
            throw new NotFoundException("Avatar for User {$command->getUserId()} does not exist");
        }

        $this->orchestrator->updateAvatar($command->getUserId(), $command->getFileContents());
    }
}
