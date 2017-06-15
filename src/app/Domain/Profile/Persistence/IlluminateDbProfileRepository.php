<?php

namespace Cocktales\Domain\Profile\Persistence;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\Hydration\Extractor;
use Cocktales\Domain\Profile\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateDbProfileRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    /**
     * IlluminateDbProfileRepository constructor.
     * @param Connection $connection
     * @param Clock $clock
     */
    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * Inserts a new profile record into the database
     *
     * @param Profile $profile
     * @return Profile
     */
    public function createProfile(Profile $profile): Profile
    {
        $profile->setCreatedDate($this->clock->now());
        $profile->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($profile));

        return $profile;
    }

    /**
     * Retrieve a profile from the database by associated User ID
     *
     * @param Uuid $id
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getProfileByUserId(Uuid $id): Profile
    {
        if (!$data = $this->table()->where('user_id', $id->toBinary())->first()) {
            throw new NotFoundException("Profile with User ID {$id->__toString()} does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * @inheritdoc
     */
    public function getProfileByUsername(string $username): Profile
    {
        $data = $this->table()->where('username', $username)->get()->first();

        if (!$data) {
            throw new NotFoundException("Profile with username '{$username}' does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * Update a profile record in the database
     *
     * @param Profile $profile
     * @return Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function updateProfile(Profile $profile): Profile
    {
        if (!$this->table()->where('user_id', $profile->getUserId()->toBinary())->exists()) {
            throw new NotFoundException("Cannot update - Profile with User ID {$profile->getUserId()->__toString()} does not exist");
        }

        $profile->setLastModifiedDate($this->clock->now());

        $this->table()->where('user_id', $profile->getUserId()->toBinary())->update((array) Extractor::toRawData($profile));

        return $profile;
    }

    /**
     * @return Builder
     */
    private function table(): Builder
    {
        return $this->connection->table('user_profile');
    }
}
