<?php

namespace Cocktales\Boundary\CocktailImage\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class UpdateCocktailImageCommand implements Command
{
    /**
     * @var string
     */
    private $cocktailId;
    /**
     * @var string
     */
    private $fileContents;

    public function __construct(string $cocktailId, string $fileContents)
    {
        $this->cocktailId = $cocktailId;
        $this->fileContents = $fileContents;
    }

    public function getCocktailId(): Uuid
    {
        return new Uuid($this->cocktailId);
    }

    public function getFileContents(): string
    {
        return $this->fileContents;
    }
}
