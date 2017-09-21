<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\Creation\Mixer;

class GetCocktailByIdCommandHandler
{
    /**
     * @var Bartender
     */
    private $bartender;
    /**
     * @var Mixer
     */
    private $mixer;

    /**
     * GetCocktailByIdCommandHandler constructor.
     * @param Bartender $bartender
     * @param Mixer $mixer
     */
    public function __construct(Bartender $bartender, Mixer $mixer)
    {
        $this->bartender = $bartender;
        $this->mixer = $mixer;
    }

    public function handle(GetCocktailByIdCommand $command): \stdClass
    {
        $cocktail = $this->mixer->mixCocktail($command->getCocktailId());

        return $this->bartender->serveCocktail($cocktail);
    }
}
