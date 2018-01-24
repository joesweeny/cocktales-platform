<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\CocktailImage;

use Cocktales\Bootstrap\Config;
use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
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
    /** @var  CocktailImageOrchestrator */
    private $orchestrator;
    /** @var  CocktailOrchestrator */
    private $cocktailOrchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->filesystem = $this->container->get(Filesystem::class);
        $this->orchestrator = $this->container->get(CocktailImageOrchestrator::class);
        $this->cocktailOrchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->container->get(Config::class)->set('log.logger', 'null');
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_and_cocktail_image_is_updated()
    {
        $this->createCocktail();
        $this->createImage();

        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail-image/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
                "user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", 
                "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", 
                "format": "base64",
                "cocktail_id": "054c755e-8f17-4e21-a64c-cbc8c3fbff34"
             }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());

        $this->deleteDirectory();
    }

    public function test_400_response_is_returned_if_body_is_missing_from_request()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail-image/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('No body in request or body is in an incorrect format', $jsend->data->errors[0]->message);
    }

    public function test_401_response_returned_if_user_id_is_not_a_valid_user_id()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail-image/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"8897fa60-e66f-41fb-86a2-9828b1785481", 
              "image": "/9j/4AAQSkZJRgABAQAAAQABAAD/", 
              "format": "base64",
              "cocktail_id": "054c755e-8f17-4e21-a64c-cbc8c3fbff34"
              }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('You are not authorized to perform this action', $jsend->data->errors[0]->message);
    }

    public function test_400_response_is_returned_if_specific_body_fields_are_missing()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/cocktail-image/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f530caab-1767-4f0c-a669-331a7bf0fc85", 
              "format": "base64",
              "cocktail_id": "054c755e-8f17-4e21-a64c-cbc8c3fbff34"
              }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'image' is missing", $jsend->data->errors[0]->message);
    }

    private function createCocktail()
    {
        $this->cocktailOrchestrator->createCocktail(
            new Cocktail(
                new Uuid('054c755e-8f17-4e21-a64c-cbc8c3fbff34'),
                $this->user->getId(),
                'Tequila Sunrise'
            )
        );
    }

    private function createImage()
    {
        $this->orchestrator->createImage(new Uuid('054c755e-8f17-4e21-a64c-cbc8c3fbff34'), 'File Content');
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/cocktail/image');
    }
}
