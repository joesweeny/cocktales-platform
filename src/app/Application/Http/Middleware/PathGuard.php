<?php

namespace Cocktales\Application\Http\Middleware;

use Cocktales\Application\Http\Session\SessionAuthenticator;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class PathGuard implements ServerMiddlewareInterface
{
    /**
     * @var string
     */
    private $exclude;
    /**
     * @var SessionAuthenticator
     */
    private $authenticator;

    /**
     * AppGuard constructor.
     * @param SessionAuthenticator $authenticator
     * @param array $exclude
     */
    public function __construct(SessionAuthenticator $authenticator, array $exclude)
    {
        $this->exclude = $exclude;
        $this->authenticator = $authenticator;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->authenticator->isLoggedIn($request) && !in_array($request->getUri()->getPath(), $this->exclude)) {
            return new RedirectResponse('/user/login');
        }

        return $delegate->process($request);
    }
}
