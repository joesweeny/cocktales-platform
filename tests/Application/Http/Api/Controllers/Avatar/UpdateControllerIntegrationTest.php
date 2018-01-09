<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

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
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Zend\Diactoros\UploadedFile;

class UpdateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Filesystem */
    private $filesystem;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_and_avatar_is_updated()
    {
        $this->createAvatar();

        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", "avatar": "/9j/4AAQSkZJRgABAQAAAQABAAD/", "format": "base64"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());

        $this->deleteDirectory();
    }

    public function test_400_response_is_returned_if_either_user_id_or_avatar_or_format_is_missing_from_request()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('bad_request', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertCount(3, $jsend->data->errors);
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
        $this->assertEquals("Required image 'avatar' is missing", $jsend->data->errors[1]->message);
        $this->assertEquals("Required field 'format' is missing", $jsend->data->errors[2]->message);
    }

    public function test_401_response_returned_if_user_id_is_not_a_valid_user_id()
    {
      $request = new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"8897fa60-e66f-41fb-86a2-9828b1785481", "avatar": "/9j/4AAQSkZJRgABAQAAAQABAAD/", "format": "base64"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('You are not authorized to perform this action', $jsend->data->errors[0]->message);
    }


    private function createAvatar()
    {
        $this->filesystem->put(new Uuid('f530caab-1767-4f0c-a669-331a7bf0fc85'), 'File Content');
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/avatar');
    }
}
