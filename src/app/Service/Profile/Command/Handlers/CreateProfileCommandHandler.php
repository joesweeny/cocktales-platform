<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Service\Profile\Command\CreateProfileCommand;

class CreateProfileCommandHandler
{
    /**
     * @var ProfileOrchestrator
     */
    private $orchestrator;

    /**
     * CreateProfileCommandHandler constructor.
     * @param ProfileOrchestrator $orchestrator
     */
    public function __construct(ProfileOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param CreateProfileCommand $command
     * @return void
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    public function handle(CreateProfileCommand $command)
    {
        $profile = $this->orchestrator->createProfileEntityFromUser($command->getUser(), $command->getUsername());

        $this->orchestrator->createProfile($profile);
    }
}
