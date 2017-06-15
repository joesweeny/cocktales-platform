<?php

namespace Cocktales\Helpers;

use Cocktales\Application\Http\HttpServer;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait UsesHttpServer
{
    /**
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function handle(ContainerInterface $container, ServerRequestInterface $request): ResponseInterface
    {
        return $container->get(HttpServer::class)->handle($request);
    }
}
