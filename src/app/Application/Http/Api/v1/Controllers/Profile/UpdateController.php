<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Boundary\Profile\Command\UpdateProfileCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UsernameValidationException;
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
            'user_id' => $body->user_id,
            'username' => $body->username,
            'first_name' => $body->first_name ?? '',
            'last_name' => $body->last_name ?? '',
            'location' => $body->location ?? '',
            'slogan' => $body->slogan ?? ''
        ];

        try {
            $this->bus->execute(new UpdateProfileCommand($data));

            return new JsendSuccessResponse();
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError("Profile for User ID {$body->user_id} does not exist")
            ]))->withStatus(404);
        } catch (UsernameValidationException $e) {
            return (new JsendErrorResponse([
                new JsendError('Username is already taken')
            ]))->withStatus(422);
        }
    }
}
