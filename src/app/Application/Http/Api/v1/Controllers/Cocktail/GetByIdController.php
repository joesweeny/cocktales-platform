<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
use Cocktales\Framework\JsendResponse\JsendNotFoundResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetByIdController
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

        if (!isset($body->cocktail_id)) {
            return new JsendBadRequestResponse([new JsendError("Required field 'cocktail_id' is missing")]);
        }

        try {
            $cocktail = $this->bus->execute(new GetCocktailByIdCommand($body->cocktail_id));

            return new JsendSuccessResponse([
                'cocktail' => $cocktail
            ]);
        } catch (NotFoundException $e) {
            return new JsendNotFoundResponse([
                new JsendError("Cocktail with ID {$body->cocktail_id} does not exist")
            ]);
        }
    }
}
