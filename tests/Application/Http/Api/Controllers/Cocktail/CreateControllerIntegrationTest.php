<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
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

class CreateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer,
        UsesContainer,
        RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  CocktailOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_having_successfully_created_a_cocktail()
    {
        $request = new ServerRequest(
            'POST',
            '/api/v1/cocktail/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
                "userId": "a88cffac-f628-445c-9f55-ae99a0542fe6",
                "cocktail": {
                        "name": "Sex on the Beach",
                        "origin": "Ibiza"
                    },
                "ingredients": [
                    {
                        "id": "801194b1-11d2-47ec-bf5b-38ddc4a4cd69",
                        "orderNumber": 1,
                        "quantity": 50,
                        "measurement": "ml"
                    },
                    {
                        "id": "7dcf85bf-a36d-446b-8f41-ef6c0dadbd8e",
                        "orderNumber": 2,
                        "quantity": 150,
                        "measurement": "ml"
                    }
                ],
                "instructions": [
                    {
                        "orderNumber": 1,
                        "text": "Shake well"
                    },
                    {
                        "orderNumber": 2,
                        "text": "Pour"
                    }
                ]
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('Sex on the Beach', $jsend->data->cocktail->name);
    }

    public function test_error_response_is_received_if_cocktail_name_already_exists()
    {
        $this->orchestrator->createCocktail((new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Sex on the Beach'
        ))->setOrigin('Made in my garage when pissed'));

        $request = new ServerRequest(
            'POST',
            '/api/v1/cocktail/create',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{
                "userId": "a88cffac-f628-445c-9f55-ae99a0542fe6",
                "cocktail": {
                        "name": "Sex on the Beach",
                        "origin": "Ibiza"
                    },
                "ingredients": [
                    {
                        "id": "801194b1-11d2-47ec-bf5b-38ddc4a4cd69",
                        "orderNumber": 1,
                        "quantity": 50,
                        "measurement": "ml"
                    },
                    {
                        "id": "7dcf85bf-a36d-446b-8f41-ef6c0dadbd8e",
                        "orderNumber": 2,
                        "quantity": 150,
                        "measurement": "ml"
                    }
                ],
                "instructions": [
                    {
                        "orderNumber": 1,
                        "text": "Shake well"
                    },
                    {
                        "orderNumber": 2,
                        "text": "Pour"
                    }
                ]
            }'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('error', $jsend->status);
        $this->assertEquals(
            'A Cocktail with the name Sex on the Beach already exists - please choose another name',
            $jsend->data->error
        );
    }
}
