<?php

namespace Cocktales\Domain\Instruction;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class InstructionPresenterTest extends TestCase
{
    public function test_convert_instruction_entity_into_public_viewable_dto()
    {
        $dto = (new InstructionPresenter)->toDto(new Instruction(
            new Uuid('49c1eb8e-484a-47c3-84de-c516ea9dc29f'),
            3,
            'Pour over ice'
        ));

        $this->assertInstanceOf(\stdClass::class, $dto);
        $this->assertEquals(3, $dto->number);
        $this->assertEquals('Pour over ice', $dto->text);
    }
}
