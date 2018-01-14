<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Cocktail;

use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientOrchestrator;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\InstructionOrchestrator;
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

class GetByIdControllerIntegrationTest extends TestCase
{
    use UsesHttpServer,
        UsesContainer,
        RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  CocktailOrchestrator */
    private $cocktailOrchestrator;
    /** @var  IngredientOrchestrator */
    private $ingredientOrchestrator;
    /** @var  CocktailIngredientOrchestrator */
    private $cocktailIngredientOrchestrator;
    /** @var  InstructionOrchestrator */
    private $instructionOrchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->cocktailOrchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->ingredientOrchestrator = $this->container->get(IngredientOrchestrator::class);
        $this->cocktailIngredientOrchestrator = $this->container->get(CocktailIngredientOrchestrator::class);
        $this->instructionOrchestrator = $this->container->get(InstructionOrchestrator::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_returns_success_response_and_cocktail_object()
    {
        $this->createCocktail();

        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-id',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"cocktail_id":"0487d724-4ca0-4942-bf64-4cc53273bc2b"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf(\stdClass::class, $jsend->data->cocktail);
    }

    public function test_returns_404_response_if_cocktail_does_not_exist()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-id',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"cocktail_id":"0487d724-4ca0-4942-bf64-4cc53273bc2b"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Cocktail with ID 0487d724-4ca0-4942-bf64-4cc53273bc2b does not exist', $jsend->data->errors[0]->message);
    }

    public function test_returns_422_response_if_cocktail_does_not_exist()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-id',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"wrong":"0487d724-4ca0-4942-bf64-4cc53273bc2b"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals("Required field 'cocktail_id' is missing", $jsend->data->errors[0]->message);
    }

    public function test_400_response_returned_if_request_body_is_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-id',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('No body in request or body is in an incorrect format', $jsend->data->errors[0]->message);
    }

    private function createCocktail()
    {
        $this->cocktailOrchestrator->createCocktail((new Cocktail(
            $cocktailId = new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Sex on the Beach'
        ))->setOrigin('Made in my garage when pissed'));

        $this->ingredientOrchestrator->insertIngredient($ingredient = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->cocktailIngredientOrchestrator->insertCocktailIngredient(new CocktailIngredient(
            $cocktailId,
            $ingredient->getId(),
            1,
            50,
            'ml'
        ));

        $this->instructionOrchestrator->insertInstruction(new Instruction(
            $cocktailId,
            1,
            'Pour over ice'
        ));
    }
}
