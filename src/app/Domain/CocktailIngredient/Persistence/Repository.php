<?php

namespace Cocktales\Domain\CocktailIngredient\Persistence;

use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;

interface Repository
{
    /**
     * Insert a new CocktailIngredient record into the database
     *
     * @param CocktailIngredient $cocktailIngredient
     */
    public function insertCocktailIngredient(CocktailIngredient $cocktailIngredient): void;
}
