<?php

namespace Cocktales\Domain\Instruction\Entity;

use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class InstructionTest extends TestCase
{
    public function test_properties_on_instruction_entity()
    {
        $instruction = (new Instruction(
            new Uuid('7a034bf8-3d52-4534-a2b6-52e6c7ac0c49'),
            new Uuid('acfe2f4e-dbe4-453f-b6cd-8854974af5e7'),
            1,
            'Pour into glass'
        ))->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00'));

        $this->assertInstanceOf(Instruction::class, $instruction);
        $this->assertEquals('7a034bf8-3d52-4534-a2b6-52e6c7ac0c49', (string) $instruction->getId());
        $this->assertEquals('acfe2f4e-dbe4-453f-b6cd-8854974af5e7', (string) $instruction->getCocktailId());
        $this->assertEquals(1, $instruction->getOrderNumber());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $instruction->getCreatedDate());
    }
}
