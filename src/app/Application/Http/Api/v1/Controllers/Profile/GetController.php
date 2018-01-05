<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Boundary\Profile\Command\GetProfileByUserIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendNotFoundResponse;
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

        if (!isset($body->user_id)) {
            return new JsendBadRequestResponse([new JsendError("Required field 'user_id' is missing")]);
        }

        try {
            $profile = $this->bus->execute(new GetProfileByUserIdCommand($body->user_id));

            return new JsendSuccessResponse(['profile' => $profile]);
        } catch (NotFoundException $e) {
            return new JsendNotFoundResponse([
                new JsendError("Profile for User ID {$body->user_id} does not exist")
            ]);
        }
    }
}
