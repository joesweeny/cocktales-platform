<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Domain\User\Hydration\Hydrator;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Boundary\User\Command\CreateUserCommand;
use Psr\Http\Message\ServerRequestInterface;

class Register
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
            $user = $this->bus->execute(new CreateUserCommand($data));

            return JsendResponse::success([
                'user' => Hydrator::toPublicViewableData($user)
            ]);
        } catch (UserEmailValidationException $e) {
            return JsendResponse::fail([
                'error' => 'A user has already registered with this email address'
            ]);
        }
    }
}
