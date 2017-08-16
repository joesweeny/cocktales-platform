<?php

namespace Cocktales\Domain\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Persistence\Repository;

class IngredientOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * IngredientOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Ingredient $ingredient
     * @throws \Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException
     */
    public function insertIngredient(Ingredient $ingredient): void
    {
        $this->repository->insertIngredient($ingredient);
    }
}
