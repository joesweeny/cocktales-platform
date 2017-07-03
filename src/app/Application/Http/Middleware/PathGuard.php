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
     * @param string $exclude
     */
    public function __construct(SessionAuthenticator $authenticator, string $exclude)
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
        if (!preg_match($this->exclude, $request->getUri()->getPath()) && !$this->authenticator->isLoggedIn($request)) {
            return new RedirectResponse('/app/auth/login');
        }

        return $delegate->process($request);
    }
}
