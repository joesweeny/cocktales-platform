<?php

namespace Cocktales\Boundary\CocktailImage\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetCocktailImageCommand implements Command
{
    /**
     * @var string
     */
    private $cocktailId;

    public function __construct(string $cocktailId)
    {
        $this->cocktailId = $cocktailId;
    }

    public function getCocktailId(): Uuid
    {
        return new Uuid($this->cocktailId);
    }
}
