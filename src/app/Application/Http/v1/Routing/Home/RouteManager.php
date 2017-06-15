<?php

namespace Cocktales\Application\Http\v1\Routing\Home;

use Cocktales\Application\Http\v1\Controllers\HomepageController;
use Cocktales\Application\Http\RouteMapper;
use FastRoute\RouteCollector;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('GET', '/app', HomepageController::class);
    }
}
