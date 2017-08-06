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
     * @throws \Cocktales\Domain\Avatar\Exception\AvatarRepositoryException
     */
    public function handle(CreateAvatarCommand $command): Avatar
    {
        return $this->orchestrator->createAvatar((new Avatar)
            ->setUserId($command->getUserId())
            ->setThumbnail($command->getThumbnail())
            ->setStandard($command->getStandardSize()));
    }
}
