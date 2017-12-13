<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\LoginUserCommand;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Controller\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class LoginController
{
    /**
     * @var CommandBus
     */
    private $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'email' => $body->email ?? '',
            'password' => $body->password ?? ''
        ];

        try {
            $token = $this->bus->execute(new LoginUserCommand($data->email, $data->password));

            return JsendResponse::success([
                'token' => $token
            ]);
        } catch (UserValidationException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to verify user credentials'
            ]);
        }
    }
}
