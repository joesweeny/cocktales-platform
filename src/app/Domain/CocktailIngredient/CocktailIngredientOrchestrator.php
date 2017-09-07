<?php

namespace Cocktales\Domain\CocktailIngredient;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\CocktailIngredient\Persistence\Repository;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class CocktailIngredientOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * CocktailIngredientOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CocktailIngredient $cocktailIngredient
     * @throws \Cocktales\Domain\CocktailIngredient\Exception\RepositoryException
     */
    public function insertCocktailIngredient(CocktailIngredient $cocktailIngredient): void
    {
        $this->repository->insertCocktailIngredient($cocktailIngredient);
    }

    public function getCocktailIngredients(Uuid $cocktailId): Collection
    {
        return $this->repository->getCocktailIngredients($cocktailId);
    }
}
