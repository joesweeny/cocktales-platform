<?php

namespace Cocktales\Domain\Avatar;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

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

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->orchestrator = $this->container->get(AvatarOrchestrator::class);
    }

    public function test_create_avatar_increases_table_count()
    {
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setThumbnail('thumbnail.jpg')
            ->setStandard('standard.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('6aa4e7ac-ad89-4184-aba5-d3c9744ac6cf'))
            ->setThumbnail('thumbnail.jpg')
            ->setStandard('standard.jpg'));

        $total = $this->connection->table('avatar')->get();

        $this->assertCount(2, $total);
    }

    public function test_exception_is_thrown_if_attempting_to_create_an_avatar_for_a_user_that_already_has_one()
    {
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setThumbnail('thumbnail.jpg')
            ->setStandard('standard.jpg'));

        $this->expectException(AvatarRepositoryException::class);
        $this->expectExceptionMessage('Avatar with dc5b6421-d452-4862-b741-d43383c3fe1d already exists');
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setThumbnail('thumbnail.jpg')
            ->setStandard('standard.jpg'));
    }
}
