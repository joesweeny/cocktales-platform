<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;

class LogoutUserCommandHandler
{
    /**
     * @var TokenOrchestrator
     */
    private $tokenOrchestrator;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(TokenOrchestrator $tokenOrchestrator, LoggerInterface $logger, Clock $clock)
    {
        $this->tokenOrchestrator = $tokenOrchestrator;
        $this->logger = $logger;
        $this->clock = $clock;
    }

    public function handle(LogoutUserCommand $command): void
    {
        try {
            $token = $this->tokenOrchestrator->getToken($command->getToken());

            $this->tokenOrchestrator->updateToken($token->setExpiry($this->clock->now()));

            return;
        } catch (NotFoundException $e) {
            $this->logger->error(
                "An attempt an invalid attempt has been made to logout. User {$command->getUserId()}. Token {$command->getToken()}"
            );
            return;
        }
    }
}
