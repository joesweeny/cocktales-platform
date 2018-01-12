<?php

namespace Cocktales\Boundary\CocktailImage\Command\Handlers;

use Cocktales\Boundary\CocktailImage\Command\UpdateCocktailImageCommand;
use Cocktales\Domain\CocktailImage\CocktailImageOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;

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

    /**
     * @param UpdateCocktailImageCommand $command
     * @throws NotFoundException
     * @return void
     */
    public function handle(UpdateCocktailImageCommand $command): void
    {
        if ($this->orchestrator->imageExists($command->getCocktailId())) {
            $this->orchestrator->updateImage($command->getCocktailId(), $command->getFileContents());
        }
        
        throw new NotFoundException("Image for Cocktail {$command->getCocktailId()} does not exist");
    }
}
