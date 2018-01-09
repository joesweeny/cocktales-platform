<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class AvatarOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  AvatarOrchestrator */
    private $orchestrator;
    /** @var  Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(AvatarOrchestrator::class);
        $this->filesystem = $this->container->get(Filesystem::class);
    }

    public function test_create_avatar_saves_contents_to_filesystem_saved_against_user_id()
    {
        $this->orchestrator->createAvatar($id = new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'), 'Some image content string');

        $this->assertTrue($this->filesystem->has('/avatar/1ce824b0-76ba-4efe-8c63-e692b702c9bf'));

        $this->removeFiles('/avatar/' . (string) $id);
    }

    public function test_an_avatar_can_be_saved_and_retrieved_from_storage()
    {
        $this->orchestrator->createAvatar($id = new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'), 'Some image content string');

        $fetched = $this->orchestrator->getAvatarByUserId($id);

        $this->assertEquals('Some image content string', $fetched);

        $this->removeFiles('/avatar/' . (string) $id);
    }

    public function test_exception_is_thrown_if_avatar_file_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Avatar for User 1ce824b0-76ba-4efe-8c63-e692b702c9bf does not exist');
        $this->orchestrator->getAvatarByUserId(new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'));
    }

    public function test_exception_is_thrown_if_avatar_file_contents_is_empty()
    {
        $this->orchestrator->createAvatar($id = new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'), '');

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unable to retrieve avatar file contents for User 1ce824b0-76ba-4efe-8c63-e692b702c9bf');
        $this->orchestrator->getAvatarByUserId(new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'));

        $this->removeFiles('/avatar/' . (string) $id);
    }

    public function test_update_avatar_updates_file_contents()
    {
        $this->orchestrator->createAvatar($id = new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'), 'Some image content string');

        $fetched = $this->orchestrator->getAvatarByUserId($id);

        $this->assertEquals('Some image content string', $fetched);

        $this->orchestrator->updateAvatar($id = new Uuid('1ce824b0-76ba-4efe-8c63-e692b702c9bf'), 'New Content');

        $fetched = $this->orchestrator->getAvatarByUserId($id);

        $this->assertEquals('New Content', $fetched);

        $this->removeFiles('/avatar/' . (string) $id);
    }

    private function removeFiles(string $file)
    {
        $this->filesystem->delete($file);
        $this->filesystem->deleteDir('/avatar');
    }
}
