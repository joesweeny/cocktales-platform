<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Boundary\Ingredient\Command\GetAllIngredientsCommand;
use Cocktales\Framework\Controller\ControllerService;
use Cocktales\Framework\Controller\JsendResponse;

class GetAllController
{
    use ControllerService;

    public function __invoke(): JsendResponse
    {
        return JsendResponse::success([
            'allIngredients' => $this->bus->execute(new GetAllIngredientsCommand)
        ]);
    }
}
