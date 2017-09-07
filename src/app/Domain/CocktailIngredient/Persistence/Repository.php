<?php

namespace Cocktales\Domain\CocktailIngredient\Persistence;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * Insert a new CocktailIngredient record into the database
     *
     * @param CocktailIngredient $cocktailIngredient
     */
    public function insertCocktailIngredient(CocktailIngredient $cocktailIngredient): void;

    /**
     * Returns a collection of CocktailIngredients linked to associated Cocktail ID
     *
     * @param Uuid $cocktailId
     * @return Collection
     */
    public function getCocktailIngredients(Uuid $cocktailId): Collection;
}
