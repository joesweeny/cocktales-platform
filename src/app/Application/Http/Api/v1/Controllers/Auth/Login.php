<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Auth;

use Cocktales\Framework\Controller\ControllerService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class Login
{
    use ControllerService;

    /**
     * @param ServerRequestInterface $request
     * @return \Zend\Diactoros\Response\HtmlResponse|RedirectResponse
     */
    public function __invoke(ServerRequestInterface $request)
    {
        if ($this->authenticator->isLoggedIn($request)) {
            return new RedirectResponse('/app');
        }

        return $this->makeTemplateResponse('/auth/login.php');
    }

    /**
     * @param ServerRequestInterface $request
     * @return \Zend\Diactoros\Response\HtmlResponse|RedirectResponse
     * @throws \Cocktales\Framework\Exception\UndefinedException
     * @throws \RuntimeException
     */
    public function submit(ServerRequestInterface $request)
    {
        $body = json_decode($request->getBody()->getContents());

        $success = $this->authenticator->login($body->email, $body->password, $request);

        if ($success) {
            return new RedirectResponse('/app');
        }

        return $this->makeTemplateResponse('/auth/login.php', ['error' => 'Email and password credentials do not match']);
    }
}
