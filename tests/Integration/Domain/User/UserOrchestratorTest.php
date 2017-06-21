<?php

namespace Cocktales\Domain\User;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserRepositoryException;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Illuminate\Database\Connection;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class UserOrchestratorTest extends TestCase
{
    use RunsMigrations;
    use CreatesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  Connection */
    private $connection;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->connection = $this->container->get(Connection::class);
    }

    public function test_create_user_adds_a_new_record_to_the_database()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('andrea@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertCount(2, $this->connection->table('user')->get());
    }

    public function test_user_can_be_retrieved_by_email()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->orchestrator->getUserByEmail('joe@example.com');

        $this->assertInstanceOf(User::class, $fetched);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_email_is_not_present_in_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with email 'fake@email.com' does not exist");
        $this->orchestrator->getUserByEmail('fake@email.com');
    }

    public function test_a_user_can_be_deleted_from_the_database()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $user =$this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertCount(1, $this->connection->table('user')->get());

        $this->orchestrator->deleteUser($user);

        $this->assertCount(0, $this->connection->table('user')->get());
    }

    public function test_a_user_can_be_retrieved_by_their_uuid()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $fetched = $this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));

        $this->assertInstanceOf(User::class, $fetched);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $fetched->getId()->__toString());
        $this->assertEquals('joe@example.com', $fetched->getEmail());
    }

    public function test_exception_is_thrown_if_id_is_not_present_in_the_database()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("User with ID 'dc5b6421-d452-4862-b741-d43383c3fe1d' does not exist");
        $this->orchestrator->getUserById(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'));
    }

    public function test_can_create_user_returns_true_if_email_is_not_present_in_the_database()
    {
        $this->assertTrue($this->orchestrator->canCreateNewUser((new User)->setEmail('joe@email.com')));
    }

    public function test_can_create_user_returns_false_if_email_is_present_in_the_database()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertFalse($this->orchestrator->canCreateNewUser((new User)->setEmail('joe@example.com')));
    }

    public function test_can_update_user_returns_true_if_updated_email_is_not_already_used_by_another_user()
    {
        $this->assertTrue($this->orchestrator->canUpdateUser('joe@email.com'));
    }

    public function test_can_update_user_returns_false_if_updated_email_is_already_used_by_another_user()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertFalse($this->orchestrator->canUpdateUser('joe@example.com'));
    }

    public function test_validate_user_password_returns_true_if_password_matches_password_stored_for_user()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertTrue($this->orchestrator->validateUserPassword(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'), 'password'));
    }

    public function test_validate_user_password_returns_false_if_password_does_not_match_password_stored_for_user()
    {
        $this->orchestrator->createUser(
            (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );

        $this->assertFalse($this->orchestrator->validateUserPassword(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'), 'wrongPassword'));
    }
}
