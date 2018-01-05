<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\GetCocktailsByUserCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetByUserController
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return JsendResponse
     */
    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $cocktails = $this->bus->execute(new GetCocktailsByUserCommand($body->userId));

        return JsendResponse::success([
            'cocktails' => $cocktails
        ]);
    }
}
