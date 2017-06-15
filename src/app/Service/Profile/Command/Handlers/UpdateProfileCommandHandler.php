<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Service\Profile\Command\UpdateProfileCommand;

class UpdateProfileCommandHandler
{
    /**
     * @var ProfileOrchestrator
     */
    private $orchestrator;

    /**
     * UpdateProfileCommandHandler constructor.
     * @param ProfileOrchestrator $orchestrator
     */
    public function __construct(ProfileOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param UpdateProfileCommand $command
     * @return \Cocktales\Domain\Profile\Entity\Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function handle(UpdateProfileCommand $command): Profile
    {
        $profile = $this->orchestrator->getProfileByUserId(new Uuid($command->getUserId()));

        return $this->orchestrator->updateProfile($profile->setFirstName($command->getFirstName())
            ->setLastName($command->getLastName())
            ->setCity($command->getCity())
            ->setCounty($command->getCounty())
            ->setSlogan($command->getSlogan()));
    }
}
