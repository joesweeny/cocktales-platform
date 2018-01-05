<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\GetCocktailsByUserCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendNotFoundResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetByUserController
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

        $cocktails = $this->bus->execute(new GetCocktailsByUserCommand($body->user_id));

        return new JsendSuccessResponse($cocktails);
    }
}
