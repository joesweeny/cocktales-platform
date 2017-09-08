<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Domain\Cocktail\Hydration\Extractor;
use Cocktales\Framework\DateTime\Clock;
use Illuminate\Database\Connection;

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
}
