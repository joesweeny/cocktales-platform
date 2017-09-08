<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;

interface Repository
{
    /**
     * Insert a new Cocktail record in the database
     *
     * @param Cocktail $cocktail
     * @throws RepositoryException
     * @return Cocktail
     */
    public function insertCocktail(Cocktail $cocktail): Cocktail;
}
