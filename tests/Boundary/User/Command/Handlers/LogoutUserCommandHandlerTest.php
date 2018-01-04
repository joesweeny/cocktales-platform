<?php

namespace Cocktales\Boundary\User\Command\Handlers;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Framework\DateTime\SystemClock;
use Cocktales\Framework\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class LogoutUserCommandHandlerTest extends TestCase
{
    /** @var  TokenOrchestrator|ObjectProphecy */
    private $orchestrator;
    /** @var  LoggerInterface|ObjectProphecy */
    private $logger;
    /** @var  LogoutUserCommandHandler */
    private $handler;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(TokenOrchestrator::class);
        $clock = new SystemClock;
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->handler = new LogoutUserCommandHandler(
            $this->orchestrator->reveal(),
            $clock,
            $this->logger->reveal()
        );
    }

    public function test_a_user_is_logged_out_successfully_by_marking_their_token_expiry_to_time_now()
    {
        $command = new LogoutUserCommand('12ba1c5d-e09d-4f04-ba82-f45d4a23ac18', '68c7f96b-a3fa-4aa7-ae84-61a8cefcff98');

        $this->orchestrator->getToken($command->getToken())->willReturn(
            $token = new SessionToken(
                $command->getToken(),
                $command->getUserId(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        );

        $this->orchestrator->updateToken($token)->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_exception_is_logged_if_a_user_attempts_to_logout_with_an_invalid_token()
    {
        $command = new LogoutUserCommand('12ba1c5d-e09d-4f04-ba82-f45d4a23ac18', '68c7f96b-a3fa-4aa7-ae84-61a8cefcff98');

        $this->orchestrator->getToken($command->getToken())->willThrow($e = new NotFoundException('Token does not exist'));

        $this->logger->error(
            "An attempt has been made to log out from User {$command->getUserId()} with Token {$command->getToken()}",
            ['exception' => $e]
        )->shouldBeCalled();

        $this->handler->handle($command);
    }
}
