<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;
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

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  AvatarOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;
    /** @var  Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(AvatarOrchestrator::class);
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_and_avatar_information_returned()
    {
        $this->orchestrator->createAvatar(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'), 'File Contents');

        $request = new ServerRequest(
            'GET',
            '/api/v1/avatar/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id" : "dc5b6421-d452-4862-b741-d43383c3fe1d"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('File Contents', $jsend->data->avatar);

        $this->deleteDirectory();
    }

    public function test_400_response_is_returned_if_user_id_field_is_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/avatar/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"wrong": "field"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
    }

    public function test_404_error_code_response_if_avatar_is_not_found()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/avatar/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id" : "dc5b6421-d452-4862-b741-d43383c3fe1d"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Avatar linked to User dc5b6421-d452-4862-b741-d43383c3fe1d does not exist', $jsend->data->errors[0]->message);
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/avatar');
    }
}
