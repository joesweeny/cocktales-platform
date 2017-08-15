<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Boundary\Avatar\Command\GetAvatarCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $avatar = $this->bus->execute(new GetAvatarCommand($body->user_id));

        return JsendResponse::success([
            'avatar' => $avatar
        ]);
    }
}
