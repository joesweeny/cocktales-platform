<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Domain\User\Hydration\Hydrator;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Service\User\Command\GetUserByIdCommand;
use Psr\Http\Message\ServerRequestInterface;

class Get
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
               'user' => Hydrator::toPublicViewableData($user)
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::fail([
                'error' => 'Unable to retrieve user'
            ]);
        }
    }
}
