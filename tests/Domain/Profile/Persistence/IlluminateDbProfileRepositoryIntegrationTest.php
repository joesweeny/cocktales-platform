<?php

namespace Cocktales\Domain\Profile\Persistence;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class IlluminateDbProfileRepositoryIntegrationTest extends TestCase
{
    use RunsMigrations;
    use CreatesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Repository */
    private $repository;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->connection = $this->container->get(Connection::class);
        $this->repository = new IlluminateDbProfileRepository(
            $this->connection ,
            new SystemClock
        );
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Repository::class, $this->repository);
    }

    public function test_create_profile_increases_table_count()
    {
        $this->repository->createProfile(
            (new Profile)->setUserId(Uuid::generate())->setUsername('joe')
        );

        $total = $this->connection->table('user_profile')->get();

        $this->assertCount(1, $total);

        $this->repository->createProfile(
            (new Profile)->setUserId(Uuid::generate())->setUsername('bob')
        );

        $total = $this->connection->table('user_profile')->get();

        $this->assertCount(2, $total);
    }

    public function test_get_profile_by_user_id_returns_a_profile_entity()
    {
        $this->repository->createProfile(
            (new Profile)
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setLocation('Essex')
                ->setSlogan('Be drunk and Merry')
        );

        $fetched = $this->repository->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $fetched);
        $this->assertEquals('b5acd30c-085e-4dee-b8a9-19e725dc62c3', $fetched->getUserId()->__toString());
        $this->assertEquals('Joe', $fetched->getFirstName());
    }

    public function test_exception_is_thrown_if_user_id_is_not_in_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Profile with User ID b5acd30c-085e-4dee-b8a9-19e725dc62c3 does not exist');
        $this->repository->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));
    }

    public function test_update_profile_correctly_updates_record_in_database()
    {
        $this->repository->createProfile(
            (new Profile)
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setLocation('Essex')
                ->setSlogan('Be drunk and Merry')
        );

        $profile = $this->repository->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('Joe', $profile->getFirstName());
        $this->assertEquals('Sweeny', $profile->getLastName());

        $profile->setFirstName('Barry')->setLastName('White');

        $this->repository->updateProfile($profile);

        $profile = $this->repository->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('Barry', $profile->getFirstName());
        $this->assertEquals('White', $profile->getLastName());
    }

    public function test_exception_is_thrown_if_attempting_to_update_a_record_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cannot update - Profile with User ID b5acd30c-085e-4dee-b8a9-19e725dc62c3 does not exist');
        $this->repository->updateProfile((new Profile)->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3')));
    }
}
