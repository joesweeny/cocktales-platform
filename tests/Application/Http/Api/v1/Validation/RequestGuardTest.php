<?php

namespace Cocktales\Application\Http\Api\v1\Validation;

use Cocktales\Application\Http\Api\v1\Validation\Avatar\AvatarRequestValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Framework\Middleware\RequestGuard;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Middleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Zend\Diactoros\Response\TextResponse;

class RequestGuardTest extends TestCase
{
    /** @var  ValidatorResolver */
    private $resolver;
    /** @var  RequestGuard */
    private $guard;
    /** @var  DelegateInterface */
    private $delegate;

    public function setUp()
    {
        $this->resolver = $this->prophesize(ValidatorResolver::class);
        $this->guard = new RequestGuard($this->resolver->reveal());
        $this->delegate = $this->prophesize(DelegateInterface::class);
    }

    public function test_user_is_allowed_to_proceed_if_request_body_fields_pass_validation()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthorizationToken' => 'a2f589dc-a879-465e-839f-54a3dc333ab3', 'AuthenticationToken' => '3e1c8889-a0c1-421e-91a7-1e2ddbec4a38'],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", "format": "base64"}'
        );

        $this->resolver->resolve('avatar')->willReturn(new AvatarRequestValidator());

        $this->delegate->process(Argument::type(ServerRequest::class))->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->guard->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }

    public function test_request_validation_exception_is_thrown_if_no_request_body_is_present()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthorizationToken' => 'a2f589dc-a879-465e-839f-54a3dc333ab3', 'AuthenticationToken' => '3e1c8889-a0c1-421e-91a7-1e2ddbec4a38']
        );

        $this->resolver->resolve('avatar')->shouldNotBeCalled();

        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage('No body in request or body is in an incorrect format');
        $this->guard->process($request, $this->delegate->reveal());
    }

    public function test_request_validation_exception_is_thrown_if_any_required_body_fields_are_missing()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthorizationToken' => 'a2f589dc-a879-465e-839f-54a3dc333ab3', 'AuthenticationToken' => '3e1c8889-a0c1-421e-91a7-1e2ddbec4a38'],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85"}'
        );

        $this->resolver->resolve('avatar')->willReturn(new AvatarRequestValidator());

        $this->expectException(RequestValidationException::class);
        $this->guard->process($request, $this->delegate->reveal());
    }
}
