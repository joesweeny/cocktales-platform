<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Bootstrap\Config;
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
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class CreateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Filesystem */
    private $filesystem;
    /** @var  SessionToken */
    private $token;
    /** @var  User */
    private $user;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->container->get(Config::class)->set('log.logger', 'null');
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_returned_if_cocktail_image_is_created_successfully()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail/image/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", 
              "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", 
              "format": "base64",
              "cocktail_id": "5ca66e0b-4ea0-4f15-bfe3-51752c6d25b3"
             }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());

        $this->deleteDirectory();
    }

    public function test_400_response_is_returned_if_required_request_body_fields_are_missing()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail/image/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('bad_request', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
        $this->assertEquals("Required field 'cocktail_id' is missing", $jsend->data->errors[1]->message);
        $this->assertEquals("Required field 'image' is missing", $jsend->data->errors[2]->message);
        $this->assertEquals("Required field 'format' is missing", $jsend->data->errors[3]->message);
    }

    public function test_401_response_is_returned_if_the_user_id_and_auth_id_do_not_match()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail/image/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"efeef6cd-04eb-464e-8c5e-82eba004408b", 
              "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", 
              "format": "base64",
              "cocktail_id": "5ca66e0b-4ea0-4f15-bfe3-51752c6d25b3"
             }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('You are not authorized to perform this action', $jsend->data->errors[0]->message);
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/cocktail/image');
    }
}