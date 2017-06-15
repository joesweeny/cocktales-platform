<?php

namespace Cocktales\Application\Http\v1\Controllers\Welcome;

use Zend\Diactoros\Response\RedirectResponse;

class WelcomeController
{

    /**
     * @return RedirectResponse
     */
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse('/app');
    }
}
