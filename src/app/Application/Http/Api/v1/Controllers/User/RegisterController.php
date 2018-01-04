<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\Session\Command\CreateSessionTokenCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Boundary\User\Command\RegisterUserCommand;
use Psr\Http\Message\ServerRequestInterface;

class RegisterController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'email' => $body->email,
            'password' => $body->password
        ];

        try {
            $user = $this->bus->execute(new RegisterUserCommand($data));
            $token = $this->bus->execute(new CreateSessionTokenCommand($user->id));

            return JsendResponse::success([
                'user' => $user,
                'token' => $token
            ]);
        } catch (UserEmailValidationException $e) {
            return JsendResponse::error([
                'error' => 'A user has already registered with this email address'
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to verify user credentials'
            ]);
        }
    }
}
