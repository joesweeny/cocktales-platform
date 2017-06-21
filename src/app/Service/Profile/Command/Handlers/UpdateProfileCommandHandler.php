<?php

namespace Cocktales\Service\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Exception\UsernameValidationException;
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
     * @return Profile
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws UsernameValidationException
     */
    public function handle(UpdateProfileCommand $command): Profile
    {
        // Throws NotFoundException
        $profile = $this->orchestrator->getProfileByUserId($command->getUserId());

        if ($profile->getUsername() != $command->getUsername()) {
            if (!$this->orchestrator->isUsernameUnique($command->getUsername())) {
                throw new UsernameValidationException("Username {$command->getUsername()} is already taken by another user");
            }
        }

        return $this->orchestrator->updateProfile(
            $profile->setUsername($command->getUsername())
                ->setFirstName($command->getFirstName())
                ->setLastName($command->getLastName())
                ->setLocation($command->getLocation())
                ->setSlogan($command->getSlogan())
        );

    }
}
