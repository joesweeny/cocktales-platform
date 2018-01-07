<?php

namespace Cocktales\Domain\CocktailImage\Persistence;

use Cocktales\Domain\CocktailImage\Entity\CocktailImage;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Insert a new CocktailImage record into the database
     *
     * @param CocktailImage $image
     * @return CocktailImage
     */
    public function insertImage(CocktailImage $image): CocktailImage;

    /**
     * @param Uuid $cocktailId
     * @throws NotFoundException
     * @return CocktailImage
     */
    public function getImageByCocktailId(Uuid $cocktailId): CocktailImage;
}
