<?php

namespace Cocktales\Boundary\Cocktail\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class GetCocktailsByUserCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;

    /**
     * GetCocktailsByUserCommand constructor.
     * @param string $userId
     */
    public function __construct(string $userId)
    {
        $this->userId = new Uuid($userId);
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
