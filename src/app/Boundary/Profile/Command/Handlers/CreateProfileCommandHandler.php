<?php

namespace Cocktales\Boundary\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Boundary\Profile\Command\CreateProfileCommand;

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
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws UsernameValidationException
     */
    public function handle(CreateProfileCommand $command): Profile
    {
        if (!$this->orchestrator->isUsernameUnique($command->getUsername())) {
            throw new UsernameValidationException("Username {$command->getUsername()} is already taken by another user");
        }

        return $this->orchestrator->createProfile($this->createProfileEntity($command));
    }

    /**
     * @param CreateProfileCommand $command
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     */
    private function createProfileEntity(CreateProfileCommand $command): Profile
    {
        return (new Profile)
            ->setUserId($command->getUserId())
            ->setUsername($command->getUsername())
            ->setFirstName($command->getFirstName())
            ->setLastName($command->getLastName())
            ->setLocation($command->getLocation())
            ->setSlogan($command->getSlogan());
    }
}
