<?php

namespace Cocktales\Domain\Instruction\Hydration;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_convert_raw_data_into_instruction_object()
    {
        $instruction = Hydrator::fromRawData((object) [
            'cocktail_id' => (new Uuid('acfe2f4e-dbe4-453f-b6cd-8854974af5e7'))->toBinary(),
            'instruction_id' => 4,
            'text' => 'On the rocks',
            'created_at' => 1489276800
        ]);

        $this->assertInstanceOf(Instruction::class, $instruction);
        $this->assertEquals('acfe2f4e-dbe4-453f-b6cd-8854974af5e7', (string) $instruction->getCocktailId());
        $this->assertEquals(4, $instruction->getOrderNumber());
        $this->assertEquals('On the rocks', $instruction->getText());
        $this->assertEquals(new \DateTimeImmutable('2017-03-12 00:00:00'), $instruction->getCreatedDate());
    }
}
