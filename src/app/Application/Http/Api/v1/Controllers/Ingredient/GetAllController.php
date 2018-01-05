<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetAllIngredientsCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\JsendResponse\JsendResponse;
use Cocktales\Framework\JsendResponse\JsendSuccessResponse;

class GetAllController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return new JsendSuccessResponse([
            'allIngredients' => $this->bus->execute(new GetAllIngredientsCommand)
        ]);
    }
}
