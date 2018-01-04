<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\ValidateUserCredentialsCommand;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Domain\User\UserPresenter;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UndefinedException;

class ValidateUserCredentialsCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    public function __construct(UserOrchestrator $userOrchestrator, UserPresenter $presenter)
    {
        $this->userOrchestrator = $userOrchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param ValidateUserCredentialsCommand $command
     * @throws \Cocktales\Domain\User\Exception\UserValidationException
     * @return \stdClass
     */
    public function handle(ValidateUserCredentialsCommand $command): \stdClass
    {
        try {
            $user = $this->userOrchestrator->getUserByEmail($command->getEmail());

            if (!$this->userOrchestrator->validateUserPassword($user->getId(), $command->getPassword())) {
                throw new UserValidationException('Unable to verify user credentials');
            }

            return $this->presenter->toDto($user);
        } catch (NotFoundException | UndefinedException $e) {
            throw new UserValidationException('Unable to verify user credentials');
        }
    }
}
