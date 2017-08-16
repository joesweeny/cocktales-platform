<?php

namespace Cocktales\Domain\Ingredient\Persistence;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException;
use Cocktales\Domain\Ingredient\Hydration\Extractor;
use Cocktales\Framework\DateTime\Clock;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateDbIngredientRepository implements Repository
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
     * IlluminateDbIngredientRepository constructor.
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
    public function insertIngredient(Ingredient $ingredient): void
    {
        if ($this->table()->where('name', $ingredient->getName())->exists()) {
            throw new IngredientRepositoryException("Ingredient with name {$ingredient->getName()} already exists");
        }

        $ingredient->setCreatedDate($this->clock->now());
        $ingredient->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($ingredient));
    }

    private function table(): Builder
    {
        return $this->connection->table('ingredient');
    }
}
