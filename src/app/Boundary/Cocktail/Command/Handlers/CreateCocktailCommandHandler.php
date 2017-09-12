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

    public function handle(CreateCocktailCommand $command)
    {
        $cocktail = $this->transformer->toCocktail($command->getCocktail(), $command->getUserId());


    }
}
