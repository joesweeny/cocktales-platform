<?php

namespace Cocktales\Boundary\CocktailImage\Command\Handlers;

use Cocktales\Boundary\CocktailImage\Command\GetCocktailImageCommand;
use Cocktales\Domain\CocktailImage\CocktailImageOrchestrator;

class GetCocktailImageCommandHandler
{
    /**
     * @var CocktailImageOrchestrator
     */
    private $orchestrator;

    public function __construct(CocktailImageOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GetCocktailImageCommand $command
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @return string
     */
    public function handle(GetCocktailImageCommand $command): string
    {
        return $this->orchestrator->getImageByCocktailId($command->getCocktailId());
    }
}
