<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->user = $this->orchestrator->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_with_user_details()
    {
        $request = new ServerRequest(
            'get',
            '/api/v1/user/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85', $jsend->data->user->id);
        $this->assertEquals('joe@joe.com', $jsend->data->user->email);
    }

    public function test_404_response_returned_if_user_details_cannot_be_retrieved()
    {
        $request = new ServerRequest(
            'get',
            '/api/v1/user/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"93449e9d-4082-4305-8840-fa1673bcf915"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            'User with ID 93449e9d-4082-4305-8840-fa1673bcf915 does not exist',
            $jsend->data->errors[0]->message
        );
    }

    public function test_400_response_returned_if_request_body_is_missing()
    {
        $request = new ServerRequest(
            'get',
            '/api/v1/user/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('No body in request or body is in an incorrect format', $jsend->data->errors[0]->message);
    }

    public function test_422_response_is_returned_if_user_id_field_is_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/user/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"wrong": "field"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
    }
}
