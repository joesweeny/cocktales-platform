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

class LoginControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
    }

    public function test_success_response_is_received()
    {
        $request = new ServerRequest('post', '/api/v1/user/login', [], '{"email":"joe@joe.com","password":"password"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertTrue(isset($jsend->data->token));
    }

    public function test_fail_response_is_sent_if_user_credentials_validation_fails()
    {
        $request = new ServerRequest('post', '/api/v1/user/login', [], '{"email":"joe@joe.com","password":"wrong"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('error', $jsend->status);
        $this->assertEquals('Unable to verify user credentials', $jsend->data->error);
    }

    public function test_fail_response_is_sent_if_either_email_or_password_is_missing()
    {
        $request = new ServerRequest('post', '/api/v1/user/login', [], '{"password":"wrong"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('error', $jsend->status);
        $this->assertEquals('Unable to verify user credentials', $jsend->data->error);
    }
}
