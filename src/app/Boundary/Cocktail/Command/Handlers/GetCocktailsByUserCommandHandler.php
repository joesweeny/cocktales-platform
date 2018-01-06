<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailsByUserCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Illuminate\Support\Collection;

class GetCocktailsByUserCommandHandler
{
    /**
     * @var CocktailOrchestrator
     */
    private $orchestrator;
    /**
     * @var Bartender
     */
    private $bartender;

    /**
     * GetCocktailsByUserCommandHandler constructor.
     * @param CocktailOrchestrator $orchestrator
     * @param Bartender $bartender
     */
    public function __construct(CocktailOrchestrator $orchestrator, Bartender $bartender)
    {
        $this->orchestrator = $orchestrator;
        $this->bartender = $bartender;
    }

    public function handle(GetCocktailsByUserCommand $command): array
    {
        $mixedCocktails = new Collection();

        foreach ($this->orchestrator->getCocktailsByUserId($command->getUserId()) as $cocktail) {
            $mixedCocktails->push($cocktail);
        }

        return $this->bartender->serveMultipleCocktails($mixedCocktails);
    }
}
