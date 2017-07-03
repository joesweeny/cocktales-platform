<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Auth;

use Cocktales\Framework\Controller\ControllerService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class Logout
{
    use ControllerService;

    public function __invoke(ServerRequestInterface $request)
    {
        $this->authenticator->logout($request);

        return new RedirectResponse('/app');
    }
}
