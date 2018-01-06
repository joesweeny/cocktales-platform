<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Cocktail;

use Cocktales\Application\Http\Api\v1\Controllers\Cocktail\CreateController;
use Cocktales\Application\Http\Api\v1\Controllers\Cocktail\GetByIdController;
use Cocktales\Application\Http\Api\v1\Controllers\Cocktail\GetByIngredientsController;
use Cocktales\Application\Http\Api\v1\Controllers\Cocktail\GetByUserController;
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
        $router->addRoute('POST', '/api/v1/cocktail/create', CreateController::class);
        $router->addRoute('GET', '/api/v1/cocktail/get-by-id', GetByIdController::class);
        $router->addRoute('GET', '/api/v1/cocktail/get-by-user', GetByUserController::class);
        $router->addRoute('GET', '/api/v1/cocktail/get-by-ingredients', GetByIngredientsController::class);
    }
}
