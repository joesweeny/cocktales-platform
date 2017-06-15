<?php

namespace Cocktales\Application\Http\v1\Routing\Welcome;

use Cocktales\Application\Http\v1\Controllers\Welcome\WelcomeController;
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
        $router->addRoute('GET', '/', WelcomeController::class);
    }
}