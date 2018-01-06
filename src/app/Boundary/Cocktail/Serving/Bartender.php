<?php

namespace Cocktales\Boundary\Cocktail\Serving;

use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientPresenter;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Cocktales\Domain\Instruction\InstructionPresenter;
use Illuminate\Support\Collection;

class Bartender
{
    /** @var CocktailPresenter  */
    private $cocktailPresenter;
    /** @var CocktailIngredientPresenter  */
    private $ingredientPresenter;
    /** @var InstructionPresenter  */
    private $instructionPresenter;
    /** @var IngredientOrchestrator  */
    private $orchestrator;

    /**
     * Bartender constructor.
     * @param CocktailPresenter $cocktailPresenter
     * @param CocktailIngredientPresenter $ingredientPresenter
     * @param InstructionPresenter $instructionPresenter
     * @param IngredientOrchestrator $orchestrator
     */
    public function __construct(
        CocktailPresenter $cocktailPresenter,
        CocktailIngredientPresenter $ingredientPresenter,
        InstructionPresenter $instructionPresenter,
        IngredientOrchestrator $orchestrator
    ) {
        $this->cocktailPresenter = $cocktailPresenter;
        $this->ingredientPresenter = $ingredientPresenter;
        $this->instructionPresenter = $instructionPresenter;
        $this->orchestrator = $orchestrator;
    }

    /**
     * Takes a Cocktail object and returns user readable Cocktail, Ingredient and Instruction information
     *
     * @param Cocktail $cocktail
     * @return \stdClass
     */
    public function serveCocktail(Cocktail $cocktail): \stdClass
    {
        $drink = (object) [];

        $drink->cocktail = $this->cocktailPresenter->toDto($cocktail);

        $drink->ingredients = $this->serveIngredients($cocktail->getIngredients());

        $drink->instructions = $this->provideInstructions($cocktail->getInstructions());

        return $drink;
    }

    /**
     * @param Collection $cocktails
     * @return array
     */
    public function serveMultipleCocktails(Collection $cocktails): array
    {
        $drinks = [
            'cocktails' => []
        ];

        foreach ($cocktails as $cocktail) {
            $drinks['cocktails'][] = (object) [
                'cocktail' => $this->cocktailPresenter->toDto($cocktail),
                'ingredients' => $this->serveIngredients($cocktail->getIngredients()),
                'instructions' => $this->provideInstructions($cocktail->getInstructions())
            ];
        }

        return $drinks;
    }

    private function serveIngredients(Collection $ingredients): array
    {
        $servedIngredients = [];

        foreach ($ingredients As $ingredient) {
            $servedIngredients[]= $this->ingredientPresenter->toDto(
                $ingredient,
                $this->orchestrator->getIngredientById($ingredient->getIngredientId())
            );
        }

        return $servedIngredients;
    }

    private function provideInstructions(Collection $instructions): array
    {
        $list = [];

        foreach ($instructions as $instruction) {
            $list[]= $this->instructionPresenter->toDto($instruction);
        }

        return $list;
    }
}
