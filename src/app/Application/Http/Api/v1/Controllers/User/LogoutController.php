<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class LogoutController
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CommandBus $bus, LoggerInterface $logger)
    {
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $token = $request->getHeaderLine('AuthorizationToken') ?? '';
        $userId = $request->getHeaderLine('AuthenticationToken') ?? '';

        if (!$token || !$userId) {
            $this->logger->error('A logout attempt has failed due to missing information', [
                'token' => $token,
                'userId' => $userId
            ]);
            return JsendResponse::fail('Unable to verify');
        }

        $this->bus->execute(new LogoutUserCommand($token, $userId));

        return JsendResponse::success();
    }
}
