<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\Profile\Command\GetProfileCommand;

class GetProfileCommandHandler
{
    /**
     * @var ProfileOrchestrator
     */
    private $orchestrator;

    /**
     * GetProfileCommandHandler constructor.
     * @param ProfileOrchestrator $orchestrator
     */
    public function __construct(ProfileOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param GetProfileCommand $command
     * @return \Cocktales\Domain\Profile\Entity\Profile
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(GetProfileCommand $command)
    {
        return $this->orchestrator->getProfileByUserId(new Uuid($command->userId()));
    }
}
