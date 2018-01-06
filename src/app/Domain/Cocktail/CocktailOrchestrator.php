<?php

namespace Cocktales\Domain\Cocktail;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Persistence\Repository;
use Cocktales\Framework\Exception\NotFoundException;
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

    /**
     * @param string $name
     * @return Cocktail
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getCocktailByName(string $name): Cocktail
    {
        return $this->repository->getCocktailByName($name);
    }

    public function getCocktailsMatchingIngredients(array $ingredientIds): Collection
    {
        return $this->repository->getCocktailsMatchingIngredients($ingredientIds);
    }

    /**
     * @param Cocktail $cocktail
     * @return bool
     */
    public function canCreateCocktail(Cocktail $cocktail): bool
    {
        try {
            $this->getCocktailByName($cocktail->getName());
            return false;
        } catch (NotFoundException $e) {
            return true;
        }
    }
}
