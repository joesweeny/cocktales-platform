<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetByIdController
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
            $cocktail = $this->bus->execute(new GetCocktailByIdCommand($body->cocktailId));

            return JsendResponse::success([
                'cocktail' => $cocktail
            ]);
        } catch (NotFoundException $e) {
            return JsendResponse::error([
                'error' => 'Cocktail does not exist'
            ]);
        }
    }
}
