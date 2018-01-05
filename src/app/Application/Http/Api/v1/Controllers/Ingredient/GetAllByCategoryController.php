<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByCategoryCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;

class GetAllByCategoryController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return new JsendSuccessResponse([
            'allIngredientsByCategory' => $this->bus->execute(new GetIngredientsSortedByCategoryCommand)
        ]);
    }
}
