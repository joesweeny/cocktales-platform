<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Boundary\User\Command\GetUserByIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     * @throws \InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        try {
            $user = $this->bus->execute(new GetUserByIdCommand($body->user_id));

            return new JsendSuccessResponse([
               'user' => $user
            ]);
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                new JsendError("User with ID {$body->user_id} does not exist")
            ]))->withStatus(404);
        }
    }
}
