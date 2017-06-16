<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Cocktales\Helpers\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class RegisterIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use CreatesContainer;
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
        $this->assertEquals('joe@email.com', $jsend->data->user->email);
    }

    public function test_fail_response_is_received_if_user_creation_process_fails()
    {
        $this->container->get(UserOrchestrator::class)->createUser(
            (new User)->setEmail('joe@email.com')->setPasswordHash(PasswordHash::createFromRaw('pass')));

        $request = new ServerRequest('post', '/api/v1/user/register', [], '{"email":"joe@email.com","password":"mypass"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals('A user has already registered with this email address', $jsend->data->error);
    }
}
