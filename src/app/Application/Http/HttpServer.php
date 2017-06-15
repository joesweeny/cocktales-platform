<?php

namespace Cocktales\Application\Http;

use Cocktales\Application\Middleware\PathGuard;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Session\Http\SessionMiddleware;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\CallableMiddlewareWrapper;
use Zend\Stratigility\MiddlewarePipe;

class HttpServer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * HttpServer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pipe = new MiddlewarePipe;

        $pipe->raiseThrowables();

        $prototype = new Response;

        return $pipe
            ->pipe('/', new CallableMiddlewareWrapper($this->container->get(SessionMiddleware::class), $prototype))
//            ->pipe('/', $this->container->get(PathGuard::class))
            ->process($request, $this->container->get(Router::class));
    }
}
