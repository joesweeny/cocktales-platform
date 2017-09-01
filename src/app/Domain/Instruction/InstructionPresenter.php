<?php

namespace Cocktales\Domain\Instruction;

use Cocktales\Domain\Instruction\Entity\Instruction;

class InstructionPresenter
{
    public function toDto(Instruction $instruction): \stdClass
    {
        return (object) [
            'number' => $instruction->getOrderNumber(),
            'text' => $instruction->getText()
        ];
    }
}
