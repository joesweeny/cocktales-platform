<?php

namespace Cocktales\Boundary\Cocktail\Creation;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class Transformer
{
    public function toCocktail(\stdClass $raw, Uuid $userId): Cocktail
    {
        return (new Cocktail(
            $raw->id ? new Uuid($raw->id) : Uuid::generate(),
            $userId,
            $raw->name
        ))->setOrigin($raw->origin);
    }

    /**
     * @param array|\stdClass[] $ingredients
     * @param Uuid $cocktailId
     * @return Collection|CocktailIngredient[]
     */
    public function toCocktailIngredients(array $ingredients, Uuid $cocktailId): Collection
    {
        $cocktailIngredients = new Collection([]);

        foreach ($ingredients as $ingredient) {
            $cocktailIngredients->push(new CocktailIngredient(
                $cocktailId,
                new Uuid($ingredient->id),
                $ingredient->orderNumber,
                $ingredient->quantity,
                $ingredient->measurement
            ));
        }

        return $cocktailIngredients;
    }

    /**
     * @param array|\stdClass[] $instructions
     * @param Uuid $cocktailId
     * @return Collection
     */
    public function toCocktailInstructions(array $instructions, Uuid $cocktailId): Collection
    {
        $cocktailInstructions = new Collection([]);

        foreach ($instructions as $instruction) {
            $cocktailInstructions->push(new Instruction(
                $cocktailId,
                $instruction->orderNumber,
                $instruction->text
            ));
        }

        return $cocktailInstructions;
    }
}
