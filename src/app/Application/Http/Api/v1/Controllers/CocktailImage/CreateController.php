<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Boundary\CocktailImage\Command\CreateCocktailImageCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $image = $body->format === 'base64' ? base64_decode($body->image) : $body->image;

        $this->bus->execute(new CreateCocktailImageCommand($body->cocktail_id, $image));

        return new JsendSuccessResponse;
    }
}
