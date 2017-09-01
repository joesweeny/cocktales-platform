<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\Hydration\Extractor;
use Cocktales\Framework\DateTime\Clock;
use Illuminate\Database\Connection;

class IlluminateDbInstructionRepository implements Repository
{
    const TABLE = 'instruction';

    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    /**
     * IlluminateDbInstructionRepository constructor.
     * @param Connection $connection
     * @param Clock $clock
     */
    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function insertInstruction(Instruction $instruction): void
    {
        $instruction->setCreatedDate($this->clock->now());

        $this->connection->table(self::TABLE)->insert((array) Extractor::toRawData($instruction));
    }
}