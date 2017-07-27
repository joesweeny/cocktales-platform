<?php

namespace Cocktales\Boundary\Profile\Command\Handlers;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Domain\Profile\ProfilePresenter;
use Cocktales\Framework\Exception\UsernameValidationException;
use Cocktales\Boundary\Profile\Command\UpdateProfileCommand;

class UpdateProfileCommandHandler
{
    /**
     * @var ProfileOrchestrator
     */
    private $orchestrator;
    /**
     * @var ProfilePresenter
     */
    private $presenter;

    /**
     * UpdateProfileCommandHandler constructor.
     * @param ProfileOrchestrator $orchestrator
     * @param ProfilePresenter $presenter
     */
    public function __construct(ProfileOrchestrator $orchestrator, ProfilePresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param UpdateProfileCommand $command
     * @return \stdClass
     * @throws \Cocktales\Framework\Exception\ActionNotSupportedException
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @throws UsernameValidationException
     */
    public function handle(UpdateProfileCommand $command): \stdClass
    {
        // Throws NotFoundException
        $profile = $this->orchestrator->getProfileByUserId($command->getUserId());

        if ($profile->getUsername() != $command->getUsername()) {
            if (!$this->orchestrator->isUsernameUnique($command->getUsername())) {
                throw new UsernameValidationException("Username {$command->getUsername()} is already taken by another user");
            }
        }

        return $this->presenter->toDto($this->orchestrator->updateProfile(
            $profile->setUsername($command->getUsername())
                ->setFirstName($command->getFirstName())
                ->setLastName($command->getLastName())
                ->setLocation($command->getLocation())
                ->setSlogan($command->getSlogan())
        ));
    }
}
