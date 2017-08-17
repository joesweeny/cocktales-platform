<?php

namespace Cocktales\Domain\Ingredient\Persistence;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
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
}
