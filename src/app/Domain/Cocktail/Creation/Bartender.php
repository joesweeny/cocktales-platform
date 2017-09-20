<?php

namespace Cocktales\Domain\Cocktail\Creation;

use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\DuplicateNameException;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientOrchestrator;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\InstructionOrchestrator;

class Bartender
{
    /**
     * @var CocktailOrchestrator
     */
    private $cocktails;
    /**
     * @var CocktailIngredientOrchestrator
     */
    private $ingredients;
    /**
     * @var InstructionOrchestrator
     */
    private $instructions;

    /**
     * Bartender constructor.
     * @param CocktailOrchestrator $cocktails
     * @param CocktailIngredientOrchestrator $ingredients
     * @param InstructionOrchestrator $instructions
     */
    public function __construct(
        CocktailOrchestrator $cocktails,
        CocktailIngredientOrchestrator $ingredients,
        InstructionOrchestrator $instructions
    ) {
        $this->cocktails = $cocktails;
        $this->ingredients = $ingredients;
        $this->instructions = $instructions;
    }

    /**
     * @param Cocktail $cocktail
     * @throws \Cocktales\Domain\Cocktail\Exception\DuplicateNameException
     * @throws \Cocktales\Domain\Cocktail\Exception\RepositoryException
     * @throws \Cocktales\Domain\CocktailIngredient\Exception\RepositoryException
     * @return Cocktail
     */
    public function create(Cocktail $cocktail): Cocktail
    {
        if (!$this->cocktails->canCreateCocktail($cocktail)) {
            throw new DuplicateNameException("A Cocktail with the name {$cocktail->getName()} already exists");
        }

        $createdCocktail = $this->cocktails->createCocktail($cocktail);

        foreach ($cocktail->getIngredients() as $ingredient) {
            $this->ingredients->insertCocktailIngredient($ingredient);
        }

        foreach ($cocktail->getInstructions() as $instruction) {
            $this->instructions->insertInstruction($instruction);
        }

        return $createdCocktail;
    }
}
