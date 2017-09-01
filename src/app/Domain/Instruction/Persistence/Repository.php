<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Domain\Instruction\Entity\Instruction;

interface Repository
{
    /**
     * Insert an Instruction record into the database
     *
     * @param Instruction $instruction
     * @return void
     */
    public function insertInstruction(Instruction $instruction): void;
}
