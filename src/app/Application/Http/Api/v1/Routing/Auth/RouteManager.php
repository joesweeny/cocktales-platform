<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Auth;

use Cocktales\Application\Http\Api\v1\Controllers\Auth\Login;
use Cocktales\Application\Http\Api\v1\Controllers\Auth\Logout;
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
        $router->addRoute('GET', '/app/auth/login', Login::class);
        $router->addRoute('POST', '/app/auth/login', Login::class . '@submit');
        $router->addRoute('GET', '/app/auth/logout', Logout::class);
    }
}
