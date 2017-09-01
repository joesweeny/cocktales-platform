<?php

namespace Cocktales\Domain\Instruction\Hydration;

use Cocktales\Domain\Instruction\Entity\Instruction;

class Extractor
{
    public static function toRawData(Instruction $instruction): \stdClass
    {
        return (object) [
            'cocktail_id' => $instruction->getCocktailId()->toBinary(),
            'instruction_id' => $instruction->getOrderNumber(),
            'text' => $instruction->getText(),
            'created_at' => $instruction->getCreatedDate()->getTimestamp()
        ];
    }
}
