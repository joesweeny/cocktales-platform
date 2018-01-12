<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\Session\Command\CreateSessionTokenCommand;
use Cocktales\Boundary\User\Command\ValidateUserCredentialsCommand;
use Cocktales\Domain\User\Exception\UserValidationException;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class LoginController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        try {
            $user = $this->bus->execute(new ValidateUserCredentialsCommand($body->email, $body->password));
            $token = $this->bus->execute(new CreateSessionTokenCommand($user->id));

            return new JsendSuccessResponse([
                'token' => $token,
                'user' => $user->id
            ]);
        } catch (UserValidationException | NotFoundException $e) {
            return (new JsendErrorResponse([
                new JsendError('Unable to verify user credentials')
            ]))->withStatus(401);
        }
    }
}
