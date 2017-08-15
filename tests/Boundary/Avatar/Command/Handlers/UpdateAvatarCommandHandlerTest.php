<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\AvatarPresenter;
use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpdateAvatarCommandHandlerTest extends TestCase
{
    public function test_handle_updates_avatar_record_in_database_and_updates_files_in_filesystem()
    {
        /** @var AvatarOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(AvatarOrchestrator::class);
        /** @var AvatarPresenter $presenter */
        $presenter = $this->prophesize(AvatarPresenter::class);
        $handler = new UpdateAvatarCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $command = new UpdateAvatarCommand(
            'c86979cf-4733-403c-80d9-c4d8e52f408f',
            new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true)
        );

        $orchestrator->getAvatarByUserId($command->getUserId())->willReturn(
            $avatar = (new Avatar)->setUserId(new Uuid('c86979cf-4733-403c-80d9-c4d8e52f408f'))->setFilename('c86979cf-4733-403c-80d9-c4d8e52f408f.jpg')
        );

        $orchestrator->saveThumbnailToStorage(
            $command->getFile(),
            '/avatar/c86979cf-4733-403c-80d9-c4d8e52f408f/thumbnail/c86979cf-4733-403c-80d9-c4d8e52f408f.jpg'
        )->shouldBeCalled();

        $orchestrator->saveStandardSizeToStorage(
            $command->getFile(),
            '/avatar/c86979cf-4733-403c-80d9-c4d8e52f408f/standard/c86979cf-4733-403c-80d9-c4d8e52f408f.jpg'
        )->shouldBeCalled();

        $orchestrator->updateAvatar($avatar->getUserId(), Argument::that(function (callable $callback) use ($avatar) {
            $callback($avatar);
            return true;
        }))->shouldBeCalled();

        $presenter->toDto(Argument::type(Avatar::class))->willReturn((object) ['filename' => 'c86979cf-4733-403c-80d9-c4d8e52f408f.jpg']);

        $handler->handle($command);
    }
}
