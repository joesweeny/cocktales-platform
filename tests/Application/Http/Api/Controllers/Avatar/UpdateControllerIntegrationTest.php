<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

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

    public function test_success_response_is_received_and_updated_avatar_is_returned()
    {
        $this->createAvatar();

        $request = (new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
               "user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.jpg', 22000, UPLOAD_ERR_OK, 'default_avatar.jpg', 'image/jpeg')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85.jpg', $jsend->data->avatar->filename);

        $this->deleteDirectory();
    }

    public function test_success_response_is_received_and_updated_avatar_with_new_extension_received_if_file_format_is_different_to_existing()
    {
        $this->createAvatar();

        $request = (new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
               "user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.png', 22000, UPLOAD_ERR_OK, 'default_avatar.png', 'image/png')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('f530caab-1767-4f0c-a669-331a7bf0fc85.png', $jsend->data->avatar->filename);

        $this->deleteDirectory();
    }

    public function test_error_response_is_return_if_either_user_id_or_avatar_file_is_missing_from_request()
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
        $this->assertCount(2, $jsend->data->errors);
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
        $this->assertEquals("Required file 'avatar' is missing", $jsend->data->errors[1]->message);
    }

    public function test_404_response_returned_if_user_id_is_not_a_valid_user_id()
    {
      $request = (new ServerRequest(
            'post',
            '/api/v1/avatar/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
               "user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.png', 22000, UPLOAD_ERR_OK, 'default_avatar.png', 'image/png')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('not_found', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
    }


    private function createAvatar()
    {
        $request = (new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
               "user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.jpg', 22000, UPLOAD_ERR_OK, 'default_avatar.jpg', 'image/jpeg')
        ]);

        $this->handle($this->container, $request);
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/avatar');
    }
}
