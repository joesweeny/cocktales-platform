<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Domain\Cocktail\Exception\DuplicateNameException;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
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
            $cocktail = $this->bus->execute(new CreateCocktailCommand(
                $body->user_id,
                $body->cocktail,
                $body->ingredients,
                $body->instructions
            ));

            return new JsendSuccessResponse([
                'cocktail_id' => $cocktail->cocktail->id
            ]);
        } catch (DuplicateNameException $e) {
            return (new JsendFailResponse([
                new JsendError($e->getMessage() . ' - please choose another name')
            ]))->withStatus(422);
        }
    }
}
