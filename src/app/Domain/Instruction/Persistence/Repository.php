<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * Insert an Instruction record into the database
     *
     * @param Instruction $instruction
     * @return void
     */
    public function insertInstruction(Instruction $instruction): void;

    /**
     * Return a collection on Instructions linked to associated Cocktail
     *
     * @param Uuid $cocktailId
     * @return Collection|Instruction[]
     */
    public function getInstructions(Uuid $cocktailId): Collection;
}
