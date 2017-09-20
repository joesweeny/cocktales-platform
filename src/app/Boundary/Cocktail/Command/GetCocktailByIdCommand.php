<?php

namespace Cocktales\Boundary\Cocktail\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetCocktailByIdCommand implements Command
{
    /**
     * @var Uuid
     */
    private $cocktailId;

    /**
     * GetCocktailByIdCommand constructor.
     * @param string $cocktailId
     */
    public function __construct(string $cocktailId)
    {
        $this->cocktailId = new Uuid($cocktailId);
    }

    /**
     * @return Uuid
     */
    public function getCocktailId(): Uuid
    {
        return $this->cocktailId;
    }
}
