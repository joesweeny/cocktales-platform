<?php

namespace Cocktales\Domain\Instruction\Hydration;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_extracts_instruction_entity_to_raw_data()
    {
        $data = Extractor::toRawData((new Instruction(
            new Uuid('acfe2f4e-dbe4-453f-b6cd-8854974af5e7'),
            1,
            'Pour into glass'
        ))->setCreatedDate(new \DateTimeImmutable('2017-03-12 00:00:00')));

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('acfe2f4e-dbe4-453f-b6cd-8854974af5e7', Uuid::createFromBinary($data->cocktail_id));
        $this->assertEquals(1, $data->instruction_id);
        $this->assertEquals('Pour into glass', $data->text);
        $this->assertEquals(1489276800, $data->created_at);
    }
}