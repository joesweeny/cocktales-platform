<?php

namespace Cocktales\Domain\Ingredient\Persistence;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * Insert an Ingredient record into the database
     *
     * @param Ingredient $ingredient
     * @throws IngredientRepositoryException
     * @return void
     */
    public function insertIngredient(Ingredient $ingredient): void;

    /**
     * Returns a collection of all Ingredients stored in the database
     *
     * @return Collection
     */
    public function getIngredients(): Collection;

    /**
     * Return a collection of Ingredients based on a specific type
     *
     * @param Type $type
     * @return Collection
     */
    public function getIngredientsByType(Type $type): Collection;

    /**
     * Return a collection of Ingredients based on a specific Category
     *
     * @param Category $category
     * @return Collection
     */
    public function getIngredientsByCategory(Category $category): Collection;
}
