<?php

namespace Cocktales\Domain\Avatar\Persistence;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IlluminateDbAvatarRepositoryIntegrationTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Connection */
    private $connection;
    /** @var  Repository */
    private $repository;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->repository = $this->container->get(Repository::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Repository::class, $this->repository);
    }

    public function test_create_avatar_increases_table_count()
    {
        $this->repository->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(1, $total);

        $this->repository->createAvatar((new Avatar)
            ->setUserId(new Uuid('6aa4e7ac-ad89-4184-aba5-d3c9744ac6cf'))
            ->setFilename('filename.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_create_an_avatar_for_a_user_that_already_has_one()
    {
        $this->repository->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $this->expectException(AvatarRepositoryException::class);
        $this->expectExceptionMessage('Avatar with User ID dc5b6421-d452-4862-b741-d43383c3fe1d already exists');
        $this->repository->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));
    }

    public function test_avatar_can_be_retrieved_by_user_id()
    {
        $this->repository->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $fetched = $this->repository->getAvatarByUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertEquals('filename.jpg', $fetched->getFilename());
    }

    public function test_exception_is_thrown_if_avatar_user_id_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Avatar with User ID dc5b6421-d452-4862-b741-d43383c3fe1d does not exist');
        $this->repository->getAvatarByUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));
    }
}
