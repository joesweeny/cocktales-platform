<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class RegisterControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
    }

    public function test_success_response_is_received()
    {
        $request = new ServerRequest('post', '/api/v1/user/register', [], '{"email":"joe@email.com","password":"mypass"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('joe@email.com', $jsend->data->user->email);
        $this->assertTrue(isset($jsend->data->token));
    }

    public function test_422_response_is_returned_if_user_creation_process_fails()
    {
        $this->container->get(UserOrchestrator::class)->createUser(
            (new User)->setEmail('joe@email.com')->setPasswordHash(PasswordHash::createFromRaw('pass')));

        $request = new ServerRequest('post', '/api/v1/user/register', [], '{"email":"joe@email.com","password":"mypass"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('A user has already registered with this email address', $jsend->data->errors[0]->message);
    }

    public function test_400_response_is_returned_if_either_email_or_password_is_missing()
    {
        $request = new ServerRequest('post', '/api/v1/user/register', [], '{"wrong": "field"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'email' is missing", $jsend->data->errors[0]->message);
        $this->assertEquals("Required field 'password' is missing", $jsend->data->errors[1]->message);
    }
}
