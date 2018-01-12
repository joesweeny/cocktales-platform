<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Boundary\CocktailImage\Command\UpdateCocktailImageCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class UpdateController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $image = $body->format === 'base64' ? base64_decode($body->image) : $body->image;

        try {
            $this->bus->execute(new UpdateCocktailImageCommand($body->user_id, $image));

            return new JsendSuccessResponse();
        } catch (NotFoundException $e) {
            return (new JsendFailResponse([
                    new JsendError("Avatar linked to User {$body->user_id} does not exist")
                ]
            ))->withStatus(404);
        }
    }
}
