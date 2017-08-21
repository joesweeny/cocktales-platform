<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByCategoryCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;

class GetAllByCategoryController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return JsendResponse::success([
            'allIngredientsByCategory' => $this->bus->execute(new GetIngredientsSortedByCategoryCommand)
        ]);
    }
}
