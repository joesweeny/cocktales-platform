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

class GetControllerIntegrationTest extends TestCase
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

    public function test_success_response_is_received_and_profile_information_returned()
    {
        $this->createProfile();

        $request = new ServerRequest(
            'GET',
            '/api/v1/profile/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id" : "b5acd30c-085e-4dee-b8a9-19e725dc62c3"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('b5acd30c-085e-4dee-b8a9-19e725dc62c3', $jsend->data->profile->user_id);
        $this->assertEquals('joe', $jsend->data->profile->username);
        $this->assertEquals('Joe', $jsend->data->profile->first_name);
        $this->assertEquals('Sweeny', $jsend->data->profile->last_name);
        $this->assertEquals('Essex', $jsend->data->profile->location);
        $this->assertEquals('Be drunk and Merry', $jsend->data->profile->slogan);
    }

    public function test_404_response_is_returned_if_profile_does_not_exist()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/profile/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id" : "b5acd30c-085e-4dee-b8a9-19e725dc62c3"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('not_found', $jsend->status);
        $this->assertEquals(
            'Profile for User ID b5acd30c-085e-4dee-b8a9-19e725dc62c3 does not exist',
            $jsend->data->errors[0]->message
        );
    }

    public function test_422_response_is_returned_if_user_id_field_is_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/profile/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"wrong": "field"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
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
