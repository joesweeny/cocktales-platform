<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Domain\CocktailImage\CocktailImageOrchestrator;
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
    /** @var  CocktailImageOrchestrator */
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
        $this->orchestrator = $this->container->get(CocktailImageOrchestrator::class);
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_returned_and_cocktail_image_returned()
    {
        $this->orchestrator->createImage(new Uuid('cecc0497-16be-40c9-adb4-80395d853e0f'), 'Image file contents');

        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/image/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"cocktail_id" : "cecc0497-16be-40c9-adb4-80395d853e0f"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Image file contents', $jsend->data->image);

        $this->deleteDirectory();
    }

    public function test_400_response_returned_if_cocktail_id_field_is_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/image/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('bad_request', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'cocktail_id' is missing", $jsend->data->errors[0]->message);
    }

    public function test_404_response_returned_if_cocktail_image_does_not_exist()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/image/get',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"cocktail_id" : "cecc0497-16be-40c9-adb4-80395d853e0f"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('not_found', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Image for Cocktail cecc0497-16be-40c9-adb4-80395d853e0f does not exist', $jsend->data->errors[0]->message);
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/cocktail');
    }
}
