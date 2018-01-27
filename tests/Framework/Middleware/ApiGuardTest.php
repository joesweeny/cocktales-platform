<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Bootstrap\Config;
use Cocktales\Boundary\Session\Command\ValidateSessionTokenCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotAuthenticatedException;
use Cocktales\Framework\Exception\NotAuthenticationException;
use Cocktales\Testing\Traits\UsesContainer;
use Interop\Http\Middleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class ApiGuardTest extends TestCase
{
    use UsesContainer;

    /** @var  CommandBus */
    private $bus;
    private $container;
    private $config;
    /** @var  ApiGuard */
    private $middleware;
    private $delegate;

    public function setUp()
    {
        $this->container = $this->createContainer();
        $this->bus = $this->prophesize(CommandBus::class);
        $this->config = $this->container->get(Config::class);
        $this->middleware = new ApiGuard($this->bus->reveal(), $this->config);
        $this->delegate = $this->prophesize(DelegateInterface::class);
        $this->container->get(Config::class)->set('base-uri', 'http://cocktales.io');
    }

    public function test_exception_is_thrown_if_no_authorization_token_header_is_provided()
    {
        $request = (new ServerRequest)->withUri(new Uri('https://cocktales.io/api/v1/user/get'));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('AuthorizationToken value not set in request header');
        $this->middleware->process($request, $this->delegate->reveal());
    }

    public function test_user_is_allowed_to_proceed_if_attempting_to_access_an_allowed_path()
    {
        $request = (new ServerRequest)->withUri(new Uri('https://cocktales.io/api/v1/user/login'));

        $this->delegate->process($request)->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }

    public function test_exception_is_thrown_if_token_provided_is_not_valid()
    {
        $request = (new ServerRequest)
            ->withUri(new Uri('https://cocktales.io/api/v1/user/get'))
            ->withHeader('AuthorizationToken', '262a3987-426e-4de4-b06f-2353c0ae1505');

        $this->bus->execute(
            new ValidateSessionTokenCommand(
                '262a3987-426e-4de4-b06f-2353c0ae1505'
            )
        )->willReturn(false);

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('AuthorizationToken value provided failed validation');
        $this->middleware->process($request, $this->delegate->reveal());
    }

    public function test_user_is_allowed_to_proceed_if_auth_validation_passes()
    {
        $request = (new ServerRequest)
            ->withUri(new Uri('https://cocktales.io/api/v1/user/get'))
            ->withHeader('AuthorizationToken', '262a3987-426e-4de4-b06f-2353c0ae1505');

        $this->bus->execute(
            new ValidateSessionTokenCommand(
                '262a3987-426e-4de4-b06f-2353c0ae1505'
            )
        )->willReturn(true);

        $this->delegate->process($request)->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }
}
