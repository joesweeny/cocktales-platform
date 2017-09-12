<?php

namespace Cocktales\Boundary\Cocktail\Creation;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;

class Transformer
{
    public function toCocktail(\stdClass $raw): Cocktail
    {
        return (new Cocktail(
            $raw->id? new Uuid($raw->id) : Uuid::generate(),
            new Uuid($raw->userId),
            $raw->name
        ))->setOrigin($raw->origin);
    }

    public function toCocktailIngredient(\stdClass $raw, Uuid $cocktailId): CocktailIngredient
    {
        return new CocktailIngredient(
            $cocktailId,
            new Uuid($raw->ingredientId),
            $raw->orderNumber,
            $raw->quantity,
            $raw->measurement
        );
    }

    public function toCocktailInstruction(\stdClass $raw, Uuid $cocktailId): Instruction
    {
        return new Instruction(
            $cocktailId,
            $raw->orderNumber,
            $raw->text
        );
    }
}
