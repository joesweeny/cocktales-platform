<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Bootstrap\Config;
use Cocktales\Boundary\Session\Command\ValidateSessionTokenCommand;
use Cocktales\Framework\CommandBus\CommandBus;
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

    public function test_user_is_redirected_to_login_page_if_no_auth_or_user_credentials_are_provided()
    {
        $request = (new ServerRequest)->withUri(new Uri('https://cocktales.io/api/v1/user/get'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://cocktales.io/user/login', $response->getHeaderLine('location'));
    }

    public function test_user_is_allowed_to_proceed_if_attempting_to_access_an_allowed_path()
    {
        $request = (new ServerRequest)->withUri(new Uri('https://cocktales.io/api/v1/user/login'));

        $this->delegate->process($request)->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }

    public function test_user_is_redirected_to_login_page_if_auth_validation_fails()
    {
        $request = (new ServerRequest)
            ->withUri(new Uri('https://cocktales.io/api/v1/user/get'))
            ->withHeader('AuthorizationToken', '262a3987-426e-4de4-b06f-2353c0ae1505')
            ->withHeader('AuthenticationToken', 'f530caab-1767-4f0c-a669-331a7bf0fc85');

        $this->bus->execute(
            new ValidateSessionTokenCommand(
                '262a3987-426e-4de4-b06f-2353c0ae1505', 'f530caab-1767-4f0c-a669-331a7bf0fc85'
            )
        )->willReturn(false);

        $response = $this->middleware->process($request, $this->delegate->reveal());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://cocktales.io/user/login', $response->getHeaderLine('location'));
    }

    public function test_user_is_allowed_to_proceed_if_auth_validation_passes()
    {
        $request = (new ServerRequest)
            ->withUri(new Uri('https://cocktales.io/api/v1/user/get'))
            ->withHeader('AuthorizationToken', '262a3987-426e-4de4-b06f-2353c0ae1505')
            ->withHeader('AuthenticationToken', 'f530caab-1767-4f0c-a669-331a7bf0fc85');

        $this->bus->execute(
            new ValidateSessionTokenCommand(
                '262a3987-426e-4de4-b06f-2353c0ae1505', 'f530caab-1767-4f0c-a669-331a7bf0fc85'
            )
        )->willReturn(true);

        $this->delegate->process($request)->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);    }
}
