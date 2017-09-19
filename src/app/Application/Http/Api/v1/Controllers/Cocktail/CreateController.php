<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Domain\Cocktail\Exception\DuplicateNameException;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
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
            $cocktail = $this->bus->execute(new CreateCocktailCommand(
                $body->userId,
                $body->cocktail,
                $body->ingredients,
                $body->instructions
            ));

            return JsendResponse::success([
                'cocktail' => $cocktail
            ]);
        } catch (DuplicateNameException $e) {
            return JsendResponse::error([
                'error' => $e->getMessage() . ' - please choose another name'
            ]);
        }
    }
}
