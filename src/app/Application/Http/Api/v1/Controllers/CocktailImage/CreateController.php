<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Boundary\CocktailImage\Command\CreateCocktailImageCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendBadRequestResponse;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;
use Psr\Http\Message\ServerRequestInterface;

class CreateController
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request): JsendResponse
    {
        $body = json_decode($request->getBody()->getContents());

        $errors = $this->validateRequest($body);
        
        if (!empty($errors)) {
            return new JsendBadRequestResponse($errors);
        }

        if (($userId = $body->user_id) !== ($authId = $request->getHeaderLine('AuthenticationToken'))) {
            $this->logError($authId, $body->cocktail_id);
            return new JsendFailResponse([new JsendError('You are not authorized to perform this action')]);
        }

        $image = $body->format === 'base64' ? base64_decode($body->image) : $body->image;

        $this->bus->execute(new CreateCocktailImageCommand($body->cocktail_id, $image));

        return new JsendSuccessResponse;
    }

    /**
     * @param mixed $body
     * @return array
     */
    private function validateRequest($body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = new JsendError("Required field 'user_id' is missing");
        }

        if (!isset($body->cocktail_id)) {
            $errors[] = new JsendError("Required field 'cocktail_id' is missing");
        }

        if (!$body->image) {
            $errors[] = new JsendError("Required field 'image' is missing");
        }

        if (!$body->format) {
            $errors[] = new JsendError("Required field 'format' is missing");
        }

        return $errors;
    }

    private function logError(string $authId, string $cocktailId)
    {
        $this->logger->error(
            "User {$authId} has attempted to create a Cocktail Image for Cocktail {$cocktailId}"
        );
    }
}
