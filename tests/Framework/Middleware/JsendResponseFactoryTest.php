<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\JsendResponse\JsendError;
use Cocktales\Framework\JsendResponse\JsendFailResponse;
use PHPUnit\Framework\TestCase;

class JsendResponseFactoryTest extends TestCase
{
    /** @var  JsendResponseFactory */
    private $middleware;

    public function setUp()
    {
        $this->middleware = new JsendResponseFactory;
    }

    public function test_correct_responses_are_returned_per_exception()
    {
        $response = $this->middleware->create(new NotFoundException('Unable to find'));
        $this->assertInstanceOf(JsendFailResponse::class, $response);
    }
}
