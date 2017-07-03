<?php

namespace Cocktales\Application\Http\Api\v1\Routing\Profile;

use Cocktales\Application\Http\Api\v1\Controllers\Profile\Create;
use Cocktales\Application\Http\Api\v1\Controllers\Profile\Update;
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
        $router->addRoute('POST', '/api/v1/profile/create', Create::class);
        $router->addRoute('POST', '/api/v1/profile/update', Update::class);
    }
}
