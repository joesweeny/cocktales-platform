<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Profile;

use Cocktales\Application\Http\Api\v1\Controllers\Profile\CreateController;
use Cocktales\Application\Http\Api\v1\Controllers\Profile\GetController;
use Cocktales\Application\Http\Api\v1\Controllers\Profile\UpdateController;
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
        $router->addRoute('POST', '/api/v1/profile/create', CreateController::class);
        $router->addRoute('POST', '/api/v1/profile/update', UpdateController::class);
        $router->addRoute('GET', '/api/v1/profile/get', GetController::class);
    }
}
