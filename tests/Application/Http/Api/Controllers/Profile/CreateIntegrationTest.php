<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;

class CreateIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  ProfileOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(ProfileOrchestrator::class);
    }

    public function test_success_response_is_received_if_profile_is_created_successfully()
    {
        $request = new ServerRequest('post', '/api/v1/profile/create', [],
            '{
                "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481",
                "username":"joe",
                "first_name":"Joe",
                "last_name":"Sweeny",
                "location":"",
                "slogan":""
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('joe', $jsend->data->profile->username);
        $this->assertEquals('Joe', $jsend->data->profile->first_name);
        $this->assertEquals('Sweeny', $jsend->data->profile->last_name);
        $this->assertEquals('', $jsend->data->profile->location);
        $this->assertEquals('', $jsend->data->profile->slogan);
    }

    public function test_fail_response_is_received_if_username_is_already_taken_by_another_user()
    {
        $this->createProfile();

        $request = new ServerRequest('post', '/api/v1/profile/create', [],
            '{
                "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481",
                "username":"joe",
                "first_name":"Joe",
                "last_name":"Sweeny",
                "location":"",
                "slogan":""
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals('Username is already taken', $jsend->data->error);
    }

    private function createProfile()
    {
        $this->orchestrator->createProfile(
            (new Profile)
                ->setUserId(new Uuid('b5acd30c-085e-4dee-b8a9-19e725dc62c3'))
                ->setUsername('joe')
                ->setFirstName('Joe')
                ->setLastName('Sweeny')
                ->setLocation('Essex')
                ->setSlogan('Be drunk and Merry')
        );
    }
}
