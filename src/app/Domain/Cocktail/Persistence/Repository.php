<?php

namespace Cocktales\Domain\Cocktail\Persistence;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\RepositoryException;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

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

    /**
     * Retrieve a Cocktail by it's unique ID
     *
     * @param Uuid $cocktailId
     * @throws NotFoundException
     * @return Cocktail
     */
    public function getCocktailById(Uuid $cocktailId): Cocktail;
}
