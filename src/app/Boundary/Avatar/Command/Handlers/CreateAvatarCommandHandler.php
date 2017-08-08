<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;

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
     * @return Avatar
     * @throws \League\Flysystem\FileExistsException
     * @throws \Cocktales\Domain\Avatar\Exception\AvatarRepositoryException
     */
    public function handle(CreateAvatarCommand $command): Avatar
    {
        $filename = $command->getUserId()->__toString() . '.' . $command->getFile()->getClientOriginalExtension();
        
        $this->orchestrator->saveThumbnailToStorage($command->getFile(), "/{$command->getUserId()}/avatar/thumbnail/" . $filename);

        $this->orchestrator->saveStandardSizeToStorage($command->getFile(), "/{$command->getUserId()}/avatar/standard/" . $filename);

        return $this->orchestrator->createAvatar((new Avatar)
            ->setUserId($command->getUserId())
            ->setFilename($filename));
    }
}
