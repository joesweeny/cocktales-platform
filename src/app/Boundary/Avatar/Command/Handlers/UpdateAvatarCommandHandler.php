<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\AvatarPresenter;
use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;

class UpdateAvatarCommandHandler
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
     * UpdateAvatarCommandHandler constructor.
     * @param AvatarOrchestrator $orchestrator
     * @param AvatarPresenter $presenter
     */
    public function __construct(AvatarOrchestrator $orchestrator, AvatarPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param UpdateAvatarCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws \League\Flysystem\FileExistsException
     */
    public function handle(UpdateAvatarCommand $command): \stdClass
    {
        $avatar = $this->orchestrator->getAvatarByUserId($command->getUserId());

        $filename = $command->getUserId()->__toString() . '.' . $command->getFile()->getClientOriginalExtension();

        $this->orchestrator->saveThumbnailToStorage($command->getFile(), "/avatar/{$command->getUserId()}/thumbnail/" . $filename);

        $this->orchestrator->saveStandardSizeToStorage($command->getFile(), "/avatar/{$command->getUserId()}/standard/" . $filename);

        return $this->presenter->toDto($this->orchestrator->updateAvatar($avatar->getUserId(), function (Avatar $avatar) use ($filename) {
            $avatar->setFilename($filename);
        }));
    }
}
