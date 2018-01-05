<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Boundary\Cocktail\Command\CreateCocktailCommand;
use Cocktales\Domain\Cocktail\Exception\DuplicateNameException;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendErrorResponse;
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

        $errors = $this->validateRequest($body);

        if (!empty($errors)) {
            return new JsendBadRequestResponse($errors);
        }

        try {
            $cocktail = $this->bus->execute(new CreateCocktailCommand(
                $body->user_id,
                $body->cocktail,
                $body->ingredients,
                $body->instructions
            ));

            return new JsendSuccessResponse([
                'cocktail' => $cocktail
            ]);

        } catch (DuplicateNameException $e) {
            return new JsendErrorResponse([
                new JsendError($e->getMessage() . ' - please choose another name')
            ]);
        }
    }

    private function validateRequest($body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = new JsendError("Required field 'user_id' is missing");
        }

        if (!isset($body->cocktail)) {
            $errors[] = new JsendError("Required 'cocktail' object is missing");
        }

        if (!isset($body->cocktail->name)) {
            $errors[] = new JsendError("Required field 'name' is missing from 'cocktail' object");
        }

        if (!isset($body->cocktail->origin)) {
            $errors[] = new JsendError("Required field 'origin' is missing from 'cocktail' object");
        }

        if (!isset($body->ingredients)) {
            $errors[] = new JsendError("Required 'ingredients' object is missing");
        }

        if (!is_array($body->ingredients)) {
            $errors[] = new JsendError("Required 'ingredients' object is not in the correct format: array");
        }

        if (!isset($body->instructions)) {
            $errors[] = new JsendError("Required 'instructions' object is missing");
        }

        if (!is_array($body->ingredients)) {
            $errors[] = new JsendError("Required 'instructions' object is not in the correct format: array");
        }

        return $errors;
    }
}
