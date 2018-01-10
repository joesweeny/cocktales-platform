<?php

namespace Cocktales\Boundary\CocktailImage\Command\Handlers;

use Cocktales\Boundary\CocktailImage\Command\CreateCocktailImageCommand;
use Cocktales\Domain\CocktailImage\CocktailImageOrchestrator;

class CreateCocktailImageCommandHandler
{
    /**
     * @var CocktailImageOrchestrator
     */
    private $orchestrator;

    public function __construct(CocktailImageOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function handle(CreateCocktailImageCommand $command): void
    {
        $this->orchestrator->createImage($command->getCocktailId(), $command->getFileContents());
    }
}
