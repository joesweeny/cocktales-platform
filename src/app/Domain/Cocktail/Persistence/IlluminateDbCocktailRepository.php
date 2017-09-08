<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Domain\Cocktail\Hydration\Extractor;
use Cocktales\Domain\Cocktail\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class IlluminateDbCocktailRepository implements Repository
{
    const TABLE = 'cocktail';
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    /**
     * IlluminateDbCocktailRepository constructor.
     * @param Connection $connection
     * @param Clock $clock
     */
    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * Insert a new Cocktail record in the database
     *
     * @param Cocktail $cocktail
     * @return Cocktail
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     */
    public function insertCocktail(Cocktail $cocktail): Cocktail
    {
        if ($this->connection->table(self::TABLE)->where('id', $cocktail->getId()->toBinary())->exists()) {
            throw new RepositoryException("Cocktail with ID {$cocktail->getId()} already exists");
        }

        $this->connection->table(self::TABLE)
            ->insert((array) Extractor::toRawData($cocktail->setCreatedDate($this->clock->now())));

        return $cocktail;
    }

    /**
     * @inheritdoc
     */
    public function getCocktailById(Uuid $cocktailId): Cocktail
    {
        if (!$data = $this->connection->table(self::TABLE)->where('id', $cocktailId->toBinary())->first()) {
            throw new NotFoundException("Cocktail with ID {$cocktailId} does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * Retrieve a collection of Cocktails linked to associated User
     *
     * @param Uuid $userId
     * @return Collection
     */
    public function getCocktailsByUserId(Uuid $userId): Collection
    {
        return Collection::make($this->connection->table(self::TABLE)->where('user_id', $userId->toBinary())->get())->map(function (\stdClass $data) {
            return Hydrator::fromRawData($data);
        });
    }
}
