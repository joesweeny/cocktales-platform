<?php

namespace Cocktales\Domain\Cocktail;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Persistence\Repository;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class CocktailOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * CocktailOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Cocktail $cocktail
     * @return Cocktail
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     */
    public function createCocktail(Cocktail $cocktail): Cocktail
    {
        return $this->repository->insertCocktail($cocktail);
    }

    /**
     * @param Uuid $id
     * @return Cocktail
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getCocktailById(Uuid $id): Cocktail
    {
        return $this->repository->getCocktailById($id);
    }

    /**
     * @param Uuid $userId
     * @return Collection
     */
    public function getCocktailsByUserId(Uuid $userId): Collection
    {
        return $this->repository->getCocktailsByUserId($userId);
    }
}
