<?php

namespace Cocktales\Boundary\CocktailImage\Command\Handlers;

use Cocktales\Boundary\CocktailImage\Command\UpdateCocktailImageCommand;
use Cocktales\Domain\CocktailImage\CocktailImageOrchestrator;

class UpdateCocktailImageCommandHandler
{
    /**
     * @var CocktailImageOrchestrator
     */
    private $orchestrator;

    public function __construct(CocktailImageOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function handle(UpdateCocktailImageCommand $command): void
    {
        $this->orchestrator->updateImage($command->getCocktailId(), $command->getFileContents());
    }
}
