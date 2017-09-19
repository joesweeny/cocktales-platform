<?php

namespace Cocktales\Boundary\Cocktail\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class CreateCocktailCommand implements Command
{
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var \stdClass
     */
    private $cocktail;
    /**
     * @var array
     */
    private $ingredients;
    /**
     * @var array
     */
    private $instructions;

    /**
     * CreateCocktailCommand constructor.
     * @param string $userId
     * @param \stdClass $cocktail
     * @param array $ingredients
     * @param array $instructions
     */
    public function __construct(string $userId, \stdClass $cocktail, array $ingredients, array $instructions)
    {
        $this->userId = new Uuid($userId);
        $this->cocktail = $cocktail;
        $this->ingredients = $ingredients;
        $this->instructions = $instructions;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getCocktail(): \stdClass
    {
        return $this->cocktail;
    }

    /**
     * @return array|\stdClass[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @return array|\stdClass[]
     */
    public function getInstructions(): array
    {
        return $this->instructions;
    }
}
