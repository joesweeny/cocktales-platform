<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Boundary\Cocktail\Creation\Transformer;
use Cocktales\Domain\Cocktail\Creation\Bartender;

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
     * CreateCocktailCommandHandler constructor.
     * @param Bartender $bartender
     * @param Transformer $transformer
     */
    public function __construct(Bartender $bartender, Transformer $transformer)
    {
        $this->bartender = $bartender;
        $this->transformer = $transformer;
    }

    /**
     * @param CreateCocktailCommand $command
     * @throws \Cocktales\Domain\CocktailIngredient\Exception\RepositoryException
     * @throws \Cocktales\Domain\Cocktail\Exception\DuplicateNameException
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     * @return void
     */
    public function handle(CreateCocktailCommand $command): void
    {
        $this->bartender->create(
            $cocktail = $this->transformer->toCocktail($command->getCocktail(), $command->getUserId()),
            $this->transformer->toCocktailIngredients($command->getIngredients(), $cocktail->getId())->toArray(),
            $this->transformer->toCocktailInstructions($command->getInstructions(), $cocktail->getId())->toArray()
        );
    }
}
