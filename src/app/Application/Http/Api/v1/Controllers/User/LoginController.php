<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\Session\Command\CreateSessionTokenCommand;
use Cocktales\Boundary\User\Command\ValidateUserCredentialsCommand;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class LoginController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'email' => $body->email ?? '',
            'password' => $body->password ?? ''
        ];

        try {
            $user = $this->bus->execute(new ValidateUserCredentialsCommand($data->email, $data->password));
            $token = $this->bus->execute(new CreateSessionTokenCommand($user->id));

            return JsendResponse::success([
                'token' => $token,
                'user' => $user->id
            ]);
        } catch (UserValidationException $e) {
            return JsendResponse::error([
                'error' => 'Unable to verify user credentials'
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to verify user credentials'
            ]);
        }
    }
}
