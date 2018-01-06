<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;

class GetCocktailByIdCommandHandler
{
    /**
     * @var Bartender
     */
    private $bartender;
    /**
     * @var CocktailOrchestrator
     */
    private $orchestrator;

    /**
     * GetCocktailByIdCommandHandler constructor.
     * @param Bartender $bartender
     * @param CocktailOrchestrator $orchestrator
     */
    public function __construct(Bartender $bartender, CocktailOrchestrator $orchestrator)
    {
        $this->bartender = $bartender;
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GetCocktailByIdCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetCocktailByIdCommand $command): \stdClass
    {
        return $this->bartender->serveCocktail(
            $this->orchestrator->getCocktailById($command->getCocktailId())
        );
    }
}
