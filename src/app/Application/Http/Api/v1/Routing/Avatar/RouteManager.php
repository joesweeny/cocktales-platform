<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Avatar;

use Cocktales\Application\Http\Api\v1\Controllers\Avatar\CreateController;
use Cocktales\Application\Http\Api\v1\Controllers\Avatar\GetController;
use Cocktales\Application\Http\Api\v1\Controllers\Avatar\UpdateController;
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
        $router->addRoute('POST', '/api/v1/avatar/create', CreateController::class);
        $router->addRoute('POST', '/api/v1/avatar/update', UpdateController::class);
        $router->addRoute('GET', '/api/v1/avatar/get', GetController::class);
    }
}
