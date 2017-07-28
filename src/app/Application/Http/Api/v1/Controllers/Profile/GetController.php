<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Boundary\Profile\Command\GetProfileByUserIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;

class GetController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        try {
            $profile = $this->bus->execute(new GetProfileByUserIdCommand($body->user_id));

            return JsendResponse::success([
                'profile' => $profile
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to retrieve profile'
            ]);
        }
    }
}
