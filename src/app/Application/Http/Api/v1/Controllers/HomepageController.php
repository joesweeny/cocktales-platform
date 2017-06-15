<?php

namespace Cocktales\Application\Http\Api\v1\Controllers;

use Cocktales\Framework\Controller\ControllerService;
use Zend\Diactoros\Response\HtmlResponse;

class HomepageController
{
    use ControllerService;

    /**
     * @return \Zend\Diactoros\Response\HtmlResponse
     */
    public function __invoke(): HtmlResponse
    {
        return $this->makeTemplateResponse('/home/home.php');
    }
}
