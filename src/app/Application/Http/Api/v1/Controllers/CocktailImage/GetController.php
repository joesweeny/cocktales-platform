<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Boundary\CocktailImage\Command\GetCocktailImageCommand;
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

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        if (!isset($body->cocktail_id)) {
            return new JsendBadRequestResponse([new JsendError("Required field 'cocktail_id' is missing")]);
        }

        try {
            $image = $this->bus->execute(new GetCocktailImageCommand($body->cocktail_id));

            return new JsendSuccessResponse([
                'image' => $image
            ]);
        } catch (NotFoundException $e) {
            return new JsendNotFoundResponse([new JsendError("Image for Cocktail {$body->cocktail_id} does not exist")]);
        }
    }
}
