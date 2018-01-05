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
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_if_avatar_is_created_successfully()
    {
        $request = (new ServerRequest(
            'post',
            '/api/v1/avatar/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
               "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481" 
            }'
            ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.jpg', 22000, UPLOAD_ERR_OK, 'default_avatar.jpg', 'image/jpeg')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('8897fa60-e66f-41fb-86a2-9828b1785481.jpg', $jsend->data->avatar->filename);

        $this->deleteDirectory();
    }

    public function test_error_response_is_received_if_user_id_or_avatar_file_is_not_provided()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/avatar/create',
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

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/avatar');
    }
}
