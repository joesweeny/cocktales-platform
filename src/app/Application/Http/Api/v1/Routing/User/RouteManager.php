<?php

namespace Cocktales\Application\Http\Api\v1\Routing\User;

use Cocktales\Application\Http\Api\v1\Controllers\User\Register;
use Cocktales\Application\Http\Api\v1\Controllers\User\Update;
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
        $router->addRoute('POST', '/api/v1/user/register', Register::class);
        $router->addRoute('POST', '/api/v1/user/update', Update::class);
    }
}
