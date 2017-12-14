<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\SessionManager;
use Psr\Log\LoggerInterface;

class LogoutUserCommandHandler
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SessionManager
     */
    private $manager;

    public function __construct(LoggerInterface $logger, SessionManager $manager)
    {
        $this->logger = $logger;
        $this->manager = $manager;
    }

    public function handle(LogoutUserCommand $command): void
    {
        try {
            $this->manager->expireToken($command->getToken());

            return;
        } catch (SessionTokenValidationException $e) {
            $this->logger->error(
                "An attempt an invalid attempt has been made to logout. User {$command->getUserId()}. Token {$command->getToken()}"
            );
            return;
        }
    }
}
