<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\UpdateAvatarCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class UpdateController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $avatar = $body->format === 'base64' ? base64_decode($body->image) : $body->image;

        try {
            $this->bus->execute(new UpdateAvatarCommand($body->user_id, $avatar));

            return new JsendSuccessResponse();
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                    new JsendError("Avatar linked to User {$body->user_id} does not exist")
                ]
            ))->withStatus(404);
        }
    }

}
