<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailsByIngredientsCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Framework\Uuid\Uuid;

class GetCocktailsByIngredientsCommandHandler
{
    /**
     * @var CocktailOrchestrator
     */
    private $orchestrator;
    /**
     * @var Bartender
     */
    private $bartender;

    public function __construct(CocktailOrchestrator $orchestrator, Bartender $bartender)
    {
        $this->orchestrator = $orchestrator;
        $this->bartender = $bartender;
    }

    /**
     * @param GetCocktailsByIngredientsCommand $command
     * @return array
     */
    public function handle(GetCocktailsByIngredientsCommand $command): array
    {
        $cocktails = $this->orchestrator->getCocktailsMatchingIngredients(
            array_map(function (string $id) {
                return new Uuid($id);
            }, $command->getIngredientIds())
        );

        return $this->bartender->serveMultipleCocktails($cocktails);
    }
}
