<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\AvatarPresenter;
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
     * @var AvatarPresenter
     */
    private $presenter;

    /**
     * CreateAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     * @param AvatarPresenter $presenter
     */
    public function __construct(AvatarOrchestrator $orchestrator, AvatarPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param CreateAvatarCommand $command
     * @return \stdClass
     * @throws \League\Flysystem\FileExistsException
     * @throws \Cocktales\Domain\Avatar\Exception\AvatarRepositoryException
     */
    public function handle(CreateAvatarCommand $command): \stdClass
    {
        $filename = $command->getUserId()->__toString() . '.' . $command->getFile()->getClientOriginalExtension();

        $this->orchestrator->saveThumbnailToStorage($command->getFile(), "/avatar/{$command->getUserId()}/thumbnail/" . $filename);

        $this->orchestrator->saveStandardSizeToStorage($command->getFile(), "/avatar/{$command->getUserId()}/standard/" . $filename);

        return $this->presenter->toDto($this->orchestrator->createAvatar((new Avatar)
            ->setUserId($command->getUserId())
            ->setFilename($filename)));
    }
}
