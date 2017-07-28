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

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
    }

    public function test_success_response_is_received_with_user_details()
    {
        $this->createUser();

        $request = new ServerRequest('get', '/api/v1/user/get', [], '{"id":"93449e9d-4082-4305-8840-fa1673bcf915"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('93449e9d-4082-4305-8840-fa1673bcf915', $jsend->data->user->id);
        $this->assertEquals('joe@mail.com', $jsend->data->user->email);
    }

    public function test_fail_response_received_if_user_details_cannot_be_retrieved()
    {
        $request = new ServerRequest('get', '/api/v1/user/get', [], '{"id":"93449e9d-4082-4305-8840-fa1673bcf915"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals('Unable to retrieve user', $jsend->data->error);
    }

    private function createUser()
    {
        $this->orchestrator->createUser(
            (new User('93449e9d-4082-4305-8840-fa1673bcf915'))
                ->setEmail('joe@mail.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
    }
}
