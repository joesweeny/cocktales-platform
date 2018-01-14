<?php

namespace Cocktales\Framework\Middleware;

use Cocktales\Boundary\Cocktail\Command\GetCocktailByIdCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotAuthorizedException;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Http\Middleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response\TextResponse;

class EntityGuardTest extends TestCase
{
    /** @var  CommandBus */
    private $bus;
    /** @var  LoggerInterface */
    private $logger;
    /** @var  EntityGuard */
    private $middleware;
    /** @var  DelegateInterface */
    private $delegate;

    public function setUp()
    {
        $this->bus = $this->prophesize(CommandBus::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->middleware = new EntityGuard($this->bus->reveal(), $this->logger->reveal());
        $this->delegate = $this->prophesize(DelegateInterface::class);
    }

    public function test_user_is_allowed_to_proceed_if_updating_user_or_profile_information_that_belongs_to_them()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthenticationToken' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", "format": "base64"}'
        );

        $this->delegate->process(Argument::type(ServerRequest::class))->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }

    public function test_not_authorized_exception_is_thrown_if_user_id_in_request_body_does_not_match_authentication_token_header()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthenticationToken' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'],
            '{"user_id":"c2166109-f443-420d-9ec2-63366c034b78"}'
        );

        $this->delegate->process(Argument::any())->shouldNotBeCalled();

        $this->logger->error('An attempt has been made to create or update a record that does not belong to the user', [
            'Auth ID' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
            'User ID' => 'c2166109-f443-420d-9ec2-63366c034b78',
            'Entity ID' => '',
            'Path' => '/api/v1/avatar/create'
        ])->shouldBeCalled();

        $this->expectException(NotAuthorizedException::class);
        $this->middleware->process($request, $this->delegate->reveal());
    }

    public function test_not_authorized_exception_is_thrown_if_authentication_token_does_not_match_retrieved_cocktail_user_id()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthenticationToken' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", "cocktail_id": "b57ec362-d77d-449e-bd16-5d779d27e6ee"}'
        );

        $this->bus->execute(new GetCocktailByIdCommand('b57ec362-d77d-449e-bd16-5d779d27e6ee'))->willReturn(
            (object) [
                'drink' => (object) [
                    'cocktail' => (object) [
                        'userId' => 'cbef76cd-f153-405e-b49b-ca78390d22dc'
                    ]
                ]
            ]
        );

        $this->delegate->process(Argument::any())->shouldNotBeCalled();

        $this->logger->error('An attempt has been made to create or update a record that does not belong to the user', [
            'Auth ID' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
            'User ID' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
            'Entity ID' => 'b57ec362-d77d-449e-bd16-5d779d27e6ee',
            'Path' => '/api/v1/avatar/update'
        ])->shouldBeCalled();

        $this->expectException(NotAuthorizedException::class);
        $this->middleware->process($request, $this->delegate->reveal());
    }

    public function test_user_is_allowed_to_proceed_if_attempting_to_alter_a_cocktail_that_belongs_to_them()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthenticationToken' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", "cocktail_id": "b57ec362-d77d-449e-bd16-5d779d27e6ee"}'
        );

        $this->bus->execute(new GetCocktailByIdCommand('b57ec362-d77d-449e-bd16-5d779d27e6ee'))->willReturn(
            (object) [
                'cocktail' => (object) [
                    'userId' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'
                ]
            ]
        );

        $this->delegate->process(Argument::type(ServerRequest::class))->willReturn($mockResponse = new TextResponse('hello!'));

        $response = $this->middleware->process($request, $this->delegate->reveal());
        $this->assertEquals($mockResponse, $response);
    }
}