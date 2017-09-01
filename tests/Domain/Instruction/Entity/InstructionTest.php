<?php

namespace Cocktales\Domain\Instruction\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class InstructionTest extends TestCase
{
    public function test_properties_on_instruction_entity()
    {
        $instruction = (new Instruction(
            new Uuid('acfe2f4e-dbe4-453f-b6cd-8854974af5e7'),
            1,
            'Pour into glass'
        ))->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'));

        $this->assertInstanceOf(Instruction::class, $instruction);
        $this->assertEquals('acfe2f4e-dbe4-453f-b6cd-8854974af5e7', (string) $instruction->getCocktailId());
        $this->assertEquals(1, $instruction->getOrderNumber());
        $this->assertEquals('Pour into glass', $instruction->getText());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $instruction->getCreatedDate());
    }
}
