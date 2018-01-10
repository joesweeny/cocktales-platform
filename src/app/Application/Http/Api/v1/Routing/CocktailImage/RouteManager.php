<?php

namespace Cocktales\Application\Http\Api\v1\Routing\CocktailImage;

use Cocktales\Application\Http\Api\v1\Controllers\CocktailImage\CreateController;
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
        $router->addRoute('POST', '/api/v1/cocktail/image/create', CreateController::class);
    }
}