<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByTypeCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;

class GetAllByTypeController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return new JsendSuccessResponse([
            'allIngredientsByType' => $this->bus->execute(new GetIngredientsSortedByTypeCommand)
        ]);
    }
}
