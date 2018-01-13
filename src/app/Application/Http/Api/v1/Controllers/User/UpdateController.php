<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\UpdateUserCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserEmailValidationException;
use Cocktales\Framework\Exception\UserPasswordValidationException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class UpdateController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $data = (object) [
            'id' => $body->user_id,
            'email' => $body->email,
            'password' => $body->password ?? '',
            'newPassword' => $body->newPassword ?? ''
        ];

        try {
            $user = $this->bus->execute(new UpdateUserCommand($data));

            return new JsendSuccessResponse([
                'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError('Unable to process request - please try again')
            ]))->withStatus(404);
        } catch (UserEmailValidationException $e) {
            return (new JsendFailResponse([
                new JsendError('A user has already registered with this email address')
            ]))->withStatus(422);
        } catch (UserPasswordValidationException $e) {
            return (new JsendFailResponse([
                new JsendError('Unable to process request - please try again')
            ]))->withStatus(401);
        }
    }
}
