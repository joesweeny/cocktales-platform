<?php

namespace Cocktales\Application\Http\Api\v1\Routing\User;

use Cocktales\Application\Http\Api\v1\Controllers\User\GetController;
use Cocktales\Application\Http\Api\v1\Controllers\User\LoginController;
use Cocktales\Application\Http\Api\v1\Controllers\User\RegisterController;
use Cocktales\Application\Http\Api\v1\Controllers\User\UpdateController;
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
        $router->addRoute('POST', '/api/v1/user/register', RegisterController::class);
        $router->addRoute('POST', '/api/v1/user/update', UpdateController::class);
        $router->addRoute('GET', '/api/v1/user/get', GetController::class);
        $router->addRoute('POST', '/api/v1/user/login', LoginController::class);
    }
}
