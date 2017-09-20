<?php

namespace Cocktales\Boundary\Cocktail\Command\Handlers;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Creation\Bartender;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientPresenter;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Cocktales\Domain\Instruction\InstructionPresenter;

class GetCocktailByIdCommandHandler
{
    /**
     * @var Bartender
     */
    private $bartender;
    /**
     * @var CocktailPresenter
     */
    private $cocktail;
    /**
     * @var CocktailIngredientPresenter
     */
    private $ingredient;
    /**
     * @var InstructionPresenter
     */
    private $instruction;
    /**
     * @var IngredientOrchestrator
     */
    private $orchestrator;

    /**
     * GetCocktailByIdCommandHandler constructor.
     * @param Bartender $bartender
     * @param CocktailPresenter $cocktail
     * @param CocktailIngredientPresenter $ingredient
     * @param InstructionPresenter $instruction
     * @param IngredientOrchestrator $orchestrator
     */
    public function __construct(
        Bartender $bartender,
        CocktailPresenter $cocktail,
        CocktailIngredientPresenter $ingredient,
        InstructionPresenter $instruction,
        IngredientOrchestrator $orchestrator
    ) {
        $this->bartender = $bartender;
        $this->cocktail = $cocktail;
        $this->ingredient = $ingredient;
        $this->instruction = $instruction;
        $this->orchestrator = $orchestrator;
    }

    public function handle(GetCocktailByIdCommand $command): \stdClass
    {
        $served = (object) [];

        $cocktail = $this->bartender->serveCocktail($command->getCocktailId());

        $served->cocktail = $this->cocktail->toDto($cocktail);

        $ingredients = [];

        foreach ($cocktail->getIngredients() as $ingredient) {
            $ingredients[]= $this->ingredient->toDto(
                $ingredient,
                $this->orchestrator->getIngredientById($ingredient->getIngredientId())
            );
        }

        $served->ingredients = $ingredients;
        
        $instructions = [];

        foreach ($cocktail->getInstructions() as $instruction) {

        }
    }
}
