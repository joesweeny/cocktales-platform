<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Ingredient;

use Cocktales\Application\Http\Api\v1\Controllers\Ingredient\GetAllByTypeController;
use Cocktales\Application\Http\Api\v1\Controllers\Ingredient\GetAllController;
use Cocktales\Framework\Routing\RouteMapper;
use FastRoute\RouteCollector;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('GET', '/api/v1/ingredient/all', GetAllController::class);
        $router->addRoute('GET', '/api/v1/ingredient/all-by-type', GetAllByTypeController::class);
    }
}
