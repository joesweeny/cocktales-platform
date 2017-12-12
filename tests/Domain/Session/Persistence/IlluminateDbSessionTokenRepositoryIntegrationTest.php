<?php

namespace Cocktales\Domain\Session\Persistence;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Connection;
use PHPUnit\Framework\TestCase;

class IlluminateDbSessionTokenRepositoryIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  Container */
    private $container;
    /** @var  Repository */
    private $repository;
    /** @var  Connection */
    private $connection;

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

    public function test_insert_token_increases_table_count()
    {
        $this->repository->insertToken(
            new SessionToken(
                new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $total = $this->connection->table('session_token')->get();
        $this->assertCount(1, $total);

        $this->repository->insertToken(
            new SessionToken(
                new Uuid('ed542c55-3fa8-40a9-bbd0-4efa0a5a211a'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $total = $this->connection->table('session_token')->get();
        $this->assertCount(2, $total);
    }

    public function test_a_session_token_can_be_retrieved_by_id()
    {
        $token = $this->repository->insertToken(
            new SessionToken(
                new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $fetched = $this->repository->getToken($token->getToken());

        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $fetched->getExpiry());
    }

    public function test_update_token_updates_a_record_in_the_database()
    {
        $token = $this->repository->insertToken(
            new SessionToken(
                new Uuid('a4a93668-6e61-4a81-93b4-b2404dbe9788'),
                new Uuid('e2ae6ff5-ae6d-4a47-a3a4-5844d5b861ed'),
                new \DateTimeImmutable('2017-12-11 20:20:02'),
                new \DateTimeImmutable('2017-12-11 20:20:02')
            )
        );

        $fetched = $this->repository->getToken($token->getToken());

        $this->assertEquals(new \DateTimeImmutable('2017-12-11 20:20:02'), $fetched->getExpiry());

        $fetched->setExpiry(new \DateTimeImmutable('2017-11-12 20:59:00'));

        $this->repository->updateToken($fetched);

        $fetched = $this->repository->getToken($token->getToken());

        $this->assertEquals(new \DateTimeImmutable('2017-11-12 20:59:00'), $fetched->getExpiry());
    }
}
