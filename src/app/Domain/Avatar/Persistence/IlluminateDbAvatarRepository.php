<?php

namespace Cocktales\Domain\Avatar\Persistence;

use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Domain\Avatar\Exception\AvatarRepositoryException;
use Cocktales\Domain\Avatar\Hydration\Extractor;
use Cocktales\Domain\Avatar\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
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
            throw new AvatarRepositoryException("Avatar with User ID {$avatar->getUserId()} already exists");
        }

        $avatar->setCreatedDate($this->clock->now());
        $avatar->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($avatar));

        return $avatar;
    }

    /**
     * @inheritdoc
     */
    public function getAvatarByUserId(Uuid $userId): Avatar
    {
        if (!$data = $this->table()->where('user_id', $userId->toBinary())->first()) {
            throw new NotFoundException("Avatar with User ID {$userId} does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * @return Builder
     */
    private function table(): Builder
    {
        return $this->connection->table('avatar');
    }
}
