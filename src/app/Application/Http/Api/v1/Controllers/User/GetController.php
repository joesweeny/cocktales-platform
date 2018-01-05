<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\GetUserByIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendResponse;
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
            $user = $this->bus->execute(new GetUserByIdCommand($body->id));

            return JsendResponse::success([
               'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to retrieve user'
            ]);
        }
    }
}
