<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Boundary\Cocktail\Creation\Transformer;
use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Creation\Bartender;
use Cocktales\Domain\Cocktail\Entity\Cocktail;

class CreateCocktailCommandHandler
{
    /**
     * @var Bartender
     */
    private $bartender;
    /**
     * @var Transformer
     */
    private $transformer;
    /**
     * @var CocktailPresenter
     */
    private $presenter;

    /**
     * CreateCocktailCommandHandler constructor.
     * @param Bartender $bartender
     * @param Transformer $transformer
     * @param CocktailPresenter $presenter
     */
    public function __construct(Bartender $bartender, Transformer $transformer, CocktailPresenter $presenter)
    {
        $this->bartender = $bartender;
        $this->transformer = $transformer;
        $this->presenter = $presenter;
    }

    /**
     * @param CreateCocktailCommand $command
     * @throws \Cocktales\Domain\CocktailIngredient\Exception\RepositoryException
     * @throws \Cocktales\Domain\Cocktail\Exception\DuplicateNameException
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     * @return \stdClass
     */
    public function handle(CreateCocktailCommand $command): \stdClass
    {
        return $this->presenter->toDto($this->bartender->create(
            $cocktail = $this->transformer->toCocktail($command->getCocktail(), $command->getUserId()),
            $this->transformer->toCocktailIngredients($command->getIngredients(), $cocktail->getId())->toArray(),
            $this->transformer->toCocktailInstructions($command->getInstructions(), $cocktail->getId())->toArray()
        ));
    }
}
