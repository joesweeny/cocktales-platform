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
    private $orchestrator;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(TokenOrchestrator $orchestrator, Clock $clock, LoggerInterface $logger)
    {
        $this->orchestrator = $orchestrator;
        $this->clock = $clock;
        $this->logger = $logger;
    }

    public function handle(LogoutUserCommand $command)
    {
        try {
            $token = $this->orchestrator->getToken($command->getToken());

            $this->orchestrator->updateToken($token->setExpiry($this->clock->now()));
        } catch (NotFoundException $e) {
            $this->logger->error(
                "An attempt has been made to log out from User {$command->getUserId()} with Token {$command->getToken()}",
                ['exception' => $e]
            );
        }
    }
}
