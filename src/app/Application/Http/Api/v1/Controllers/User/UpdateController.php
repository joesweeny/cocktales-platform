<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\UpdateUserCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Framework\Exception\UserPasswordValidationException;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class UpdateController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \RuntimeException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'id' => $body->id,
            'email' => $body->email,
            'oldPassword' => $body->oldPassword ?? '',
            'newPassword' => $body->newPassword ?? ''
        ];

        try {
            $user = $this->bus->execute(new UpdateUserCommand($data));

            return JsendResponse::success([
                'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to process request - please try again'
            ]);
        } catch (UserEmailValidationException $e) {
            return JsendResponse::fail([
                'error' => 'A user has already registered with this email address'
            ]);
        } catch (UserPasswordValidationException $e) {
            return JsendResponse::fail([
                'error' => 'Password does not match the password on record - please try again'
            ]);
        }
    }
}
