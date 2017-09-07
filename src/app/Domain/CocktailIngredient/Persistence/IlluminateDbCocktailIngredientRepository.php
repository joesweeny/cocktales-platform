<?php

namespace Cocktales\Domain\CocktailIngredient\Persistence;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\CocktailIngredient\Exception\RepositoryException;
use Cocktales\Domain\CocktailIngredient\Hydration\Extractor;
use Cocktales\Framework\DateTime\Clock;
use Illuminate\Database\Connection;

class IlluminateDbCocktailIngredientRepository implements Repository
{
    const TABLE = 'cocktail_ingredient';
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    /**
     * IlluminateDbCocktailIngredientRepository constructor.
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
    public function insertCocktailIngredient(CocktailIngredient $cocktailIngredient): void
    {
        if ($this->connection->table(self::TABLE)
            ->where('cocktail_id', $cocktailIngredient->getCocktailId()->toBinary())
            ->where('ingredient_id', $cocktailIngredient->getIngredientId()->toBinary())
            ->exists()
        ) {
            throw new RepositoryException(
                "CocktailIngredient with Cocktail {$cocktailIngredient->getCocktailId()} and Ingredient 
                {$cocktailIngredient->getIngredientId()} already exists"
            );
        }

        $cocktailIngredient->setCreatedDate($this->clock->now());

        $this->connection->table(self::TABLE)->insert((array) Extractor::toRawData($cocktailIngredient));
    }
}
