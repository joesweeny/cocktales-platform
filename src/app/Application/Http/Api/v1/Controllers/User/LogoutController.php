<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\LogoutUserCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Controller\JsendResponse;
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
        $token = $request->getHeader('AuthorizationToken')[0] ?? '';
        $userId = $request->getHeader('AuthorizationToken')[1] ?? '';

        if (!$token || $userId) {
            $this->logger->error('A logout attempt has failed due to missing information', [
                'token' => $token,
                'userId' => $userId
            ]);
            return JsendResponse::success();
        }

        $this->bus->execute(new LogoutUserCommand($token, $userId));

        return JsendResponse::success();
    }
}
