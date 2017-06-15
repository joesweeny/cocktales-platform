<?php

namespace Cocktales\Domain\Profile;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ProfileOrchestratorIntegrationTest extends TestCase
{
    use RunsMigrations;
    use CreatesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(ProfileOrchestrator::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_create_profile_entity_from_user_returns_a_profile_entity_with_user_id_set()
    {
        $profile = $this->orchestrator->createProfileEntityFromUser(
            (new User)->setId(new Uuid('acbde855-3b9d-4ad8-801d-78fffcda2be7')), 'joe'
        );

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('acbde855-3b9d-4ad8-801d-78fffcda2be7', $profile->getUserId()->__toString());
        $this->assertEquals('joe', $profile->getUsername());
    }

    public function test_create_profile_increases_table_count()
    {
        $this->orchestrator->createProfile(
            (new Profile)->setId(Uuid::generate())->setUserId(Uuid::generate())->setUsername('joe')
        );

        $total = $this->connection->table('user_profile')->get();

        $this->assertCount(1, $total);

        $this->orchestrator->createProfile(
            (new Profile)->setId(Uuid::generate())->setUserId(Uuid::generate())->setUsername('andrea')
        );

        $total = $this->connection->table('user_profile')->get();

        $this->assertCount(2, $total);
    }

    public function test_update_profile_correctly_updates_record_in_database()
    {
        $this->orchestrator->createProfile(
            (new Profile)
                ->setId(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'))
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setCity('Romford')
                ->setCounty('Essex')
                ->setSlogan('Be drunk and Merry')
        );

        $profile = $this->orchestrator->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('Joe', $profile->getFirstName());
        $this->assertEquals('Sweeny', $profile->getLastName());

        $profile->setFirstName('Barry')->setLastName('White');

        $this->orchestrator->updateProfile($profile);

        $profile = $this->orchestrator->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('Barry', $profile->getFirstName());
        $this->assertEquals('White', $profile->getLastName());
    }

    public function test_exception_is_thrown_if_attempting_to_update_a_record_that_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Cannot update - Profile with User ID b5acd30c-085e-4dee-b8a9-19e725dc62c3 does not exist');
        $this->orchestrator->updateProfile((new Profile)->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3')));
    }

    public function test_get_profile_by_user_id_returns_a_profile_entity()
    {
        $this->orchestrator->createProfile(
            (new Profile)
                ->setId(new Uuid('03622d29-9e1d-499e-a9dd-9fcd12b4fab9'))
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setCity('Romford')
                ->setCounty('Essex')
                ->setSlogan('Be drunk and Merry')
        );

        $fetched = $this->orchestrator->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));

        $this->assertInstanceOf(Profile::class, $fetched);
        $this->assertEquals('03622d29-9e1d-499e-a9dd-9fcd12b4fab9', $fetched->getId()->__toString());
        $this->assertEquals('b5acd30c-085e-4dee-b8a9-19e725dc62c3', $fetched->getUserId()->__toString());
        $this->assertEquals('Joe', $fetched->getFirstName());
    }

    public function test_exception_is_thrown_if_user_id_is_not_in_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Profile with User ID b5acd30c-085e-4dee-b8a9-19e725dc62c3 does not exist');
        $this->orchestrator->getProfileByUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'));
    }
}
