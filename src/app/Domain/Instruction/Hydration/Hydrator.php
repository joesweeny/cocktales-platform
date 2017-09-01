<?php

namespace Cocktales\Domain\Instruction\Hydration;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;

class Hydrator
{
    public static function fromRawData(\stdClass $data): Instruction
    {
        return (new Instruction(
            Uuid::createFromBinary($data->cocktail_id),
            $data->instruction_id,
            $data->text
        ))->setCreatedDate((new \DateTimeImmutable)->setTimestamp($data->created_at));
    }
}
