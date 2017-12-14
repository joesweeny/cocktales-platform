<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Domain\Session\Exception\SessionTokenValidationException;
use Cocktales\Domain\Session\SessionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LogoutUserCommandHandlerTest extends TestCase
{
    /** @var  LoggerInterface */
    private $logger;
    /** @var  SessionManager */
    private $manager;
    /** @var  LogoutUserCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->manager = $this->prophesize(SessionManager::class);
        $this->handler = new LogoutUserCommandHandler(
            $this->logger->reveal(),
            $this->manager->reveal()
        );
    }

    public function test_handle_expires_token_by_passing_to_session_manager()
    {
        $command = new LogoutUserCommand('fb940bf8-5b20-4b0a-8a21-9f160d8f8c7a', '9f9e4c54-364d-409c-9ca2-c8087271fef7');

        $this->manager->expireToken($command->getToken())->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_exception_is_caught_and_logged_if_token_is_invalid()
    {
        $command = new LogoutUserCommand('fb940bf8-5b20-4b0a-8a21-9f160d8f8c7a', '9f9e4c54-364d-409c-9ca2-c8087271fef7');

        $this->manager->expireToken($command->getToken())->willThrow(new SessionTokenValidationException);

        $this->logger->error(
            "An attempt an invalid attempt has been made to logout. User {$command->getUserId()}. Token {$command->getToken()}"
        )->shouldBeCalled();

        $this->handler->handle($command);
    }
}
