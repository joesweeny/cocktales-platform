<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Domain\Cocktail\Hydration\Extractor;
use Cocktales\Domain\Cocktail\Hydration\Hydrator;
use Cocktales\Domain\CocktailIngredient\Persistence\Repository as CocktailIngredientRepo;
use Cocktales\Domain\Instruction\Persistence\Repository as InstructionRepo;
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
     * @var CocktailIngredientRepo
     */
    private $ciRepo;
    /**
     * @var InstructionRepo
     */
    private $instructionRepo;

    /**
     * IlluminateDbCocktailRepository constructor.
     * @param Connection $connection
     * @param Clock $clock
     * @param CocktailIngredientRepo $ciRepo
     * @param InstructionRepo $instructionRepo
     */
    public function __construct(
        Connection $connection,
        Clock $clock,
        CocktailIngredientRepo $ciRepo,
        InstructionRepo $instructionRepo
    ) {
        $this->connection = $connection;
        $this->clock = $clock;
        $this->ciRepo = $ciRepo;
        $this->instructionRepo = $instructionRepo;
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

        $cocktail = Hydrator::fromRawData($data)
            ->setIngredients($this->ciRepo->getCocktailIngredients($cocktailId))
            ->setInstructions($this->instructionRepo->getInstructions($cocktailId));

        return $cocktail;
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
            return Hydrator::fromRawData($data)
                ->setIngredients($this->ciRepo->getCocktailIngredients(Uuid::createFromBinary($data->id)))
                ->setInstructions($this->instructionRepo->getInstructions(Uuid::createFromBinary($data->id)));
        });
    }

    /**
     * @param string $name
     * @throws NotFoundException
     * @return Cocktail
     */
    public function getCocktailByName(string $name): Cocktail
    {
        if (!$data = $this->connection->table(self::TABLE)->where('name', $name)->first()) {
            throw new NotFoundException("Cocktail '{$name}' does not exist");
        }

        $cocktail = Hydrator::fromRawData($data);

        return $cocktail
            ->setIngredients($this->ciRepo->getCocktailIngredients($cocktail->getId()))
            ->setInstructions($this->instructionRepo->getInstructions($cocktail->getId()));
    }

    public function getCocktailsMatchingIngredients(array $ingredientIds)
    {
        $ingredientIds = array_map(function (Uuid $id) {
                return $id->toBinary();
        }, $ingredientIds);

        return Collection::make($this->connection->table('cocktail')->selectRaw(
                '*, (SELECT
                count(ingredient_id)
                FROM cocktail_ingredient
                WHERE ingredient_id IN (' . implode(',', array_fill(0, count($ingredientIds), '?')) . ')
                AND cocktail.id = cocktail_id)
                AS count', [$ingredientIds]
            )
            ->groupBy('id')
            ->having('count', '>=', 1)
            ->orderByDesc('count')
            ->orderBy('name')
            ->get())->map(function (\stdClass $data) {
            return Hydrator::fromRawData($data)
                ->setIngredients($this->ciRepo->getCocktailIngredients(Uuid::createFromBinary($data->id)))
                ->setInstructions($this->instructionRepo->getInstructions(Uuid::createFromBinary($data->id)));
        });
    }
}
