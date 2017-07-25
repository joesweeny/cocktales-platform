<?php

namespace Cocktales\Application\Http\App\Routing;

use FastRoute\RouteCollector;
use Cocktales\Application\Http\App\Controllers\HomepageController;
use Cocktales\Framework\Routing\RouteMapper;

class RouteManager implements RouteMapper
{
    /**
     * @param RouteCollector $router
     * @return void
     */
    public function map(RouteCollector $router)
    {
        $router->addRoute('GET', '/', HomepageController::class);
    }
}
