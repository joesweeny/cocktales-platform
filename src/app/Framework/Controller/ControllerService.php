<?php

namespace Cocktales\Framework\Controller;

use Cocktales\Application\Http\Session\SessionAuthenticator;
use Cocktales\Framework\CommandBus\CommandBus;
use Twig_Environment;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

trait ControllerService
{
    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var SessionAuthenticator
     */
    private $authenticator;

    /**
     * ControllerService constructor.
     * @param Twig_Environment $twig
     * @param CommandBus $bus
     * @param SessionAuthenticator $authenticator
     */
    public function __construct(Twig_Environment $twig, CommandBus $bus, SessionAuthenticator $authenticator)
    {
        $this->twig = $twig;
        $this->bus = $bus;
        $this->authenticator = $authenticator;
    }

    public function makeTemplateResponse(string $path, array $context = [])
    {
        $content = $this->twig->render($path, $context);

        return new HtmlResponse($content);
    }

    public function makeTemplateRedirectResponse(string $path, array $headers = [])
    {
        return new RedirectResponse($path, 302, $headers);
    }
}
