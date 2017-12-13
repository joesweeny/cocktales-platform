<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Bootstrap\Config;
use Cocktales\Boundary\Session\Command\ValidateSessionTokenCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class ApiGuard implements ServerMiddlewareInterface
{
    private $allowedPaths = [
        '/api/v1/user/login',
        '/api/v1/user/register'
    ];

    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var Config
     */
    private $config;

    public function __construct(CommandBus $bus, Config $config)
    {
        $this->bus = $bus;
        $this->config = $config;
    }

    /**
     * Process an incoming client or server request and return a response,
     * optionally delegating to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $uri = $request->getUri();

        // Stratigility mutates the `Uri` object before passing it to middleware by
        // removing the path used to bind the middleware. If the OriginalMessages
        // middleware is used we can access the originalUri attribute.
        if ($request->getAttribute('originalUri')) {
            $uri = $request->getAttribute('originalUri');
        }
        
        if (in_array($uri->getPath(), $this->allowedPaths, true)) {
            return $delegate->process($request);
        }

        $token = $request->getHeader('AuthorizationToken')[0] ?? '';
        $userId = $request->getHeader('AuthorizationToken')[1] ?? '';

        if (!$token || !$userId) {
            return new RedirectResponse("{$this->config->get('base-uri')}/user/login");
        }

        if (!$this->bus->execute(new ValidateSessionTokenCommand($token, $userId))) {
            return new RedirectResponse("{$this->config->get('base-uri')}/user/login");
        }

        return $delegate->process($request);
    }
}
