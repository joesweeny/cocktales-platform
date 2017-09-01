<?php

namespace Cocktales\Domain\Instruction;

use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\Persistence\Repository;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;

class InstructionOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * InstructionOrchestrator constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function insertInstruction(Instruction $instruction): void
    {
        $this->repository->insertInstruction($instruction);
    }

    /**
     * @param Uuid $cocktailId
     * @return Collection|Instruction[]
     */
    public function getInstructions(Uuid $cocktailId): Collection
    {
        return $this->repository->getInstructions($cocktailId);
    }
}
