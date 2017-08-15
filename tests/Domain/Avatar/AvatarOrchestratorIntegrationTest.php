<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AvatarOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  AvatarOrchestrator */
    private $orchestrator;
    /** @var  Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->orchestrator = $this->container->get(AvatarOrchestrator::class);
        $this->filesystem = $this->container->get(Filesystem::class);
    }

    public function test_create_avatar_increases_table_count()
    {
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('6aa4e7ac-ad89-4184-aba5-d3c9744ac6cf'))
            ->setFilename('filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_create_an_avatar_for_a_user_that_already_has_one()
    {
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $this->expectException(AvatarRepositoryException::class);
        $this->expectExceptionMessage('Avatar with dc5b6421-d452-4862-b741-d43383c3fe1d already exists');
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));
    }

    public function test_thumbnail_image_is_created_and_save_to_storage()
    {
        $file = new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true);

        $this->orchestrator->saveThumbnailToStorage($file, $newFile = '/tests/test.jpg');

        $this->assertFileExists('./src/public/img/tests/test.jpg');

        $this->removeFiles($newFile);
    }

    public function test_standard_image_is_created_and_save_to_storage()
    {
        $file = new UploadedFile('./src/public/img/default_avatar.jpg', 'default_avatar.jpg', 'image/jpeg', 22000, UPLOAD_ERR_OK, true);

        $this->orchestrator->saveThumbnailToStorage($file, $newFile = '/tests/test.jpg');

        $this->assertFileExists('./src/public/img/tests/test.jpg');

        $this->removeFiles($newFile);
    }

    private function removeFiles(string $file)
    {
        $this->filesystem->delete($file);
        $this->filesystem->deleteDir('/tests');
    }
}
