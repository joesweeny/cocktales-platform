<?php

namespace Cocktales\Boundary\Avatar\Command\Handlers;

use Cocktales\Boundary\Avatar\Command\CreateAvatarCommand;
use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateAvatarCommandHandlerTest extends TestCase
{
    public function test_avatar_thumbnail_and_standard_size_are_created_and_entity_saved_to_repository()
    {
        /** @var AvatarOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(AvatarOrchestrator::class);
        $handler = new CreateAvatarCommandHandler($orchestrator->reveal());

        $command = new CreateAvatarCommand(
            'c86979cf-4733-403c-80d9-c4d8e52f408f',
            new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true)
        );

        $orchestrator->saveThumbnailToStorage(
            $command->getFile(),
            '/c86979cf-4733-403c-80d9-c4d8e52f408f/avatar/thumbnail/c86979cf-4733-403c-80d9-c4d8e52f408f.jpg'
        )->shouldBeCalled();

        $orchestrator->saveStandardSizeToStorage(
            $command->getFile(),
            '/c86979cf-4733-403c-80d9-c4d8e52f408f/avatar/standard/c86979cf-4733-403c-80d9-c4d8e52f408f.jpg'
        )->shouldBeCalled();

        $orchestrator->createAvatar((new Avatar)->setUserId($command->getUserId())->setFilename('c86979cf-4733-403c-80d9-c4d8e52f408f.jpg'))->shouldBeCalled();

        $handler->handle($command);
    }
}
