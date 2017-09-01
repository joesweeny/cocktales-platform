<?php

namespace Cocktales\Domain\Instruction\Persistence;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\Hydration\Extractor;
use Cocktales\Domain\Instruction\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class IlluminateDbInstructionRepository implements Repository
{
    const TABLE = 'cocktail_instruction';

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

    /**
     * @inheritdoc
     */
    public function getInstructions(Uuid $cocktailId): Collection
    {
        return Collection::make($this->connection->table(self::TABLE)->orderBy('ingredient_id')->get())->map(function (\stdClass $data) {
            return Hydrator::fromRawData($data);
        });
    }
}