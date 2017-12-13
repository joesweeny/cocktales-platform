<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\ValidateUserCredentialsCommand;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;

class ValidationUserCredentialsCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    public function __construct(UserOrchestrator $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    public function handle(ValidateUserCredentialsCommand $command): bool
    {
        try {
            $user = $this->orchestrator->getUserByEmail($command->getEmail());

            if (!$this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())) {
                return false;
            }

            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }
}
