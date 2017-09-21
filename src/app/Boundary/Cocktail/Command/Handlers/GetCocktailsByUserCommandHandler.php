<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailsByUserCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Creation\Mixer;
use Illuminate\Support\Collection;

class GetCocktailsByUserCommandHandler
{
    /**
     * @var CocktailOrchestrator
     */
    private $orchestrator;
    /**
     * @var Mixer
     */
    private $mixer;
    /**
     * @var Bartender
     */
    private $bartender;

    /**
     * GetCocktailsByUserCommandHandler constructor.
     * @param CocktailOrchestrator $orchestrator
     * @param Mixer $mixer
     * @param Bartender $bartender
     */
    public function __construct(CocktailOrchestrator $orchestrator, Mixer $mixer, Bartender $bartender)
    {
        $this->orchestrator = $orchestrator;
        $this->mixer = $mixer;
        $this->bartender = $bartender;
    }

    public function handle(GetCocktailsByUserCommand $command): \stdClass
    {
        $mixedCocktails = new Collection();

        foreach ($this->orchestrator->getCocktailsByUserId($command->getUserId()) as $cocktail) {
            $mixedCocktails->push($this->mixer->mixCocktail($cocktail));
        }

        return $this->bartender->serveMultipleCocktails($mixedCocktails);
    }
}
