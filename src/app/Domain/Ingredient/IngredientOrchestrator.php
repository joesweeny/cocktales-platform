<?php

namespace Cocktales\Domain\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\Persistence\Repository;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

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

    public function getIngredients(): Collection
    {
        return $this->repository->getIngredients();
    }

    public function getIngredientsByType(Type $type): Collection
    {
        return $this->repository->getIngredientsByType($type);
    }

    public function getIngredientsByCategory(Category $category): Collection
    {
        return $this->repository->getIngredientsByCategory($category);
    }

    /**
     * @param Uuid $id
     * @return Ingredient
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getIngredientById(Uuid $id): Ingredient
    {
        return $this->repository->getIngredientById($id);
    }
}
