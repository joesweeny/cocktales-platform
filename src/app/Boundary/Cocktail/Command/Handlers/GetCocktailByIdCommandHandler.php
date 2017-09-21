<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;
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
     * @var CocktailOrchestrator
     */
    private $orchestrator;

    /**
     * GetCocktailByIdCommandHandler constructor.
     * @param Bartender $bartender
     * @param Mixer $mixer
     * @param CocktailOrchestrator $orchestrator
     */
    public function __construct(Bartender $bartender, Mixer $mixer, CocktailOrchestrator $orchestrator)
    {
        $this->bartender = $bartender;
        $this->mixer = $mixer;
        $this->orchestrator = $orchestrator;
    }

    public function handle(GetCocktailByIdCommand $command): \stdClass
    {
        $cocktail = $this->mixer->mixCocktail($this->orchestrator->getCocktailById($command->getCocktailId()));

        return $this->bartender->serveCocktail($cocktail);
    }
}
