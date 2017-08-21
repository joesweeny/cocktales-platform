<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByTypeCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;

class GetAllByTypeController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return JsendResponse::success([
            'allIngredientsByType' => $this->bus->execute(new GetIngredientsSortedByTypeCommand)
        ]);
    }
}
