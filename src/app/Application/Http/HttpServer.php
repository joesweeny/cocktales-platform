<?php

namespace Cocktales\Application\Http;

use Cocktales\Framework\Middleware\ApiGuard;
use Cocktales\Framework\Middleware\ErrorHandler;
use Cocktales\Framework\Middleware\HtmlErrorResponseFactory;
use Cocktales\Framework\Middleware\PsrLogger;
use Interop\Container\ContainerInterface;
use Cocktales\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Session\Http\SessionMiddleware;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\CallableMiddlewareWrapper;
use Zend\Stratigility\Middleware\OriginalMessages;
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

    /**
     * Handle a HTTP request and return an HTTP response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $pipe = new MiddlewarePipe;

        $pipe->raiseThrowables();

        $prototype = new Response;

        return $pipe
            ->pipe('/', new CallableMiddlewareWrapper(new OriginalMessages, new Response))

            ->pipe('/', new ErrorHandler(
                $this->container->get(HtmlErrorResponseFactory::class),
                $this->container->get(PsrLogger::class)
            ))

            ->pipe('/api', $this->container->get(ApiGuard::class))

            ->process($request, $this->container->get(Router::class));
    }
}
