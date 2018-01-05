<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\GetAvatarCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        if (!isset($body->user_id)) {
            return new JsendErrorResponse([new JsendError("Required field 'User Id' is missing")]);
        }

        $avatar = $this->bus->execute(new GetAvatarCommand($body->user_id));

        return new JsendSuccessResponse([
            'avatar' => $avatar
        ]);
    }
}
