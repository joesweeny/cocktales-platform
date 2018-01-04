<?php

namespace Cocktales\Boundary\Session\Command\Handlers;

use Cocktales\Boundary\Session\Command\CreateSessionTokenCommand;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\UserOrchestrator;

class CreateSessionTokenCommandHandler
{
    /**
     * @var TokenOrchestrator
     */
    private $tokenOrchestrator;
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;

    public function __construct(TokenOrchestrator $tokenOrchestrator, UserOrchestrator $userOrchestrator)
    {
        $this->tokenOrchestrator = $tokenOrchestrator;
        $this->userOrchestrator = $userOrchestrator;
    }

    /**
     * @param CreateSessionTokenCommand $command
     * @throws \Cocktales\Framework\Exception\NotFoundException
     * @return string
     */
    public function handle(CreateSessionTokenCommand $command): string
    {
        $user = $this->userOrchestrator->getUserById($command->getUserId());

        $token = $this->tokenOrchestrator->createToken($user->getId());

        return (string) $token->getToken();
    }
}
