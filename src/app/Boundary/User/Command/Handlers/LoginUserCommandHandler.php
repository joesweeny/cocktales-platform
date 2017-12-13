<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LoginUserCommand;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;

class LoginUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var TokenOrchestrator
     */
    private $tokenOrchestrator;

    public function __construct(UserOrchestrator $orchestrator, TokenOrchestrator $tokenOrchestrator)
    {
        $this->orchestrator = $orchestrator;
        $this->tokenOrchestrator = $tokenOrchestrator;
    }

    /**
     * @param LoginUserCommand $command
     * @return string
     * @throws UserValidationException
     */
    public function handle(LoginUserCommand $command): string
    {
        try {
            $user = $this->orchestrator->getUserByEmail($command->getEmail());

            if (!$this->orchestrator->validateUserPassword($user->getId(), $command->getPassword())) {
                throw new UserValidationException('Unable to verify user credentials');
            }

            return (string) $this->tokenOrchestrator->createToken($user->getId())->getToken();
        } catch (NotFoundException $e) {
            throw new UserValidationException('Unable to verify user credentials');
        }
    }
}
