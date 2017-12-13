<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Profile;

use Cocktales\Domain\Profile\Entity\Profile;
use Cocktales\Domain\Profile\ProfileOrchestrator;
use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class UpdateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  ProfileOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(ProfileOrchestrator::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());

    }

    public function test_success_response_is_received()
    {
        $this->createProfile();

        $request = new ServerRequest(
            'post',
            '/api/v1/profile/update',
            ['AuthorizationToken' => [(string) $this->token->getToken(), (string) $this->user->getId()]],
            '{
                "user_id":"b5acd30c-085e-4dee-b8a9-19e725dc62c3",
                "username":"joe",
                "first_name":"Joe",
                "last_name":"Sweeny",
                "location":"Consett",
                "slogan":"Loving life"
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('joe', $jsend->data->profile->username);
        $this->assertEquals('Joe', $jsend->data->profile->first_name);
        $this->assertEquals('Sweeny', $jsend->data->profile->last_name);
        $this->assertEquals('Consett', $jsend->data->profile->location);
        $this->assertEquals('Loving life', $jsend->data->profile->slogan);
    }

    public function test_fail_response_is_received_if_user_id_is_not_found()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/profile/update',
            ['AuthorizationToken' => [(string) $this->token->getToken(), (string) $this->user->getId()]],
            '{
                "user_id":"b5acd30c-085e-4dee-b8a9-19e725dc62c3",
                "username":"joe",
                "first_name":"Joe",
                "last_name":"Sweeny",
                "location":"Consett",
                "slogan":"Loving life"
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals('Unable to process request - please try again', $jsend->data->error);
    }

    public function test_fail_response_received_if_username_is_already_taken_by_another_user()
    {
        $this->createProfile();

        $this->createAdditionalProfile();

        $request = new ServerRequest(
            'post',
            '/api/v1/profile/update',
            ['AuthorizationToken' => [(string) $this->token->getToken(), (string) $this->user->getId()]],
            '{
                "user_id":"b5acd30c-085e-4dee-b8a9-19e725dc62c3",
                "username":"andrea",
                "first_name":"Joe",
                "last_name":"Sweeny",
                "location":"Consett",
                "slogan":"Loving life"
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

    private function createAdditionalProfile()
    {
        $this->orchestrator->createProfile(
            (new Profile)
                ->setUserId(new Uuid('93449e9d-4082-4305-8840-fa1673bcf915'))
                ->setUsername('andrea')
                ->setFirstName('Andrea')
                ->setLastName('Sweeny')
                ->setLocation('Gateshead')
                ->setSlogan('Be drunk')
        );
    }
}
