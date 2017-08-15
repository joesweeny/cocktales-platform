<?php

namespace Cocktales\Domain\Avatar\Persistence;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Domain\Avatar\Hydration\Extractor;
use Cocktales\Framework\DateTime\Clock;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateDbAvatarRepository implements Repository
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
     * IlluminateDbAvatarRepository constructor.
     * @param Connection $connection
     * @param Clock $clock
     */
    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function createAvatar(Avatar $avatar): Avatar
    {
        if ($this->table()->where('user_id', $avatar->getUserId()->toBinary())->exists()) {
            throw new AvatarRepositoryException("Avatar with {$avatar->getUserId()} already exists");
        }

        $avatar->setCreatedDate($this->clock->now());
        $avatar->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($avatar));

        return $avatar;
    }

    /**
     * @return Builder
     */
    private function table(): Builder
    {
        return $this->connection->table('avatar');
    }
}
