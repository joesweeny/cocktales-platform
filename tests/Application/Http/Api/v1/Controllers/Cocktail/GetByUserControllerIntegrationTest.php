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

class GetByUserControllerIntegrationTest extends TestCase
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

    public function test_returns_success_response_with_multiple_cocktail_information()
    {
        $this->createCocktails();

        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-user',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f5a366cf-15a0-4aca-a19e-e77c3e71815f"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(is_array($jsend->data->cocktails));
        $this->assertNotEmpty($jsend->data->cocktails);
    }

    public function test_returns_success_and_an_emtpy_array_if_user_does_not_have_cocktails()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-user',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"f5a366cf-15a0-4aca-a19e-e77c3e71815f"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEmpty($jsend->data->cocktails);
    }

    public function test_400_returned_if_required_specific_body_requirements_are_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-user',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_400_returned_if_required_specific_body_fields_are_missing()
    {
        $request = new ServerRequest(
            'GET',
            '/api/v1/cocktail/get-by-user',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"wrong": ["e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5"]}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("Required field 'user_id' is missing", $jsend->data->errors[0]->message);
    }

    private function createCocktails()
    {
        $this->cocktailOrchestrator->createCocktail((new Cocktail(
            $cocktailId1 = new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Sex on the Beach'
        ))->setOrigin('Made in my garage when pissed'));

        $this->ingredientOrchestrator->insertIngredient($ingredient = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->cocktailIngredientOrchestrator->insertCocktailIngredient(new CocktailIngredient(
            $cocktailId1,
            $ingredient->getId(),
            1,
            50,
            'ml'
        ));

        $this->instructionOrchestrator->insertInstruction(new Instruction(
            $cocktailId1,
            1,
            'Pour over ice'
        ));

        $this->cocktailOrchestrator->createCocktail((new Cocktail(
            $cocktailId2 = new Uuid('2935e1f6-99de-45f2-8c8d-409543c90c44'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'Sweeny Slammer'
        ))->setOrigin('In the pub in Essex'));

        $this->ingredientOrchestrator->insertIngredient($ingredient = (new Ingredient('7f886854-2bac-49d5-9fae-a04e64213552'))
            ->setName('Orange Juice')
            ->setCategory(Category::MIXER())
            ->setType(Type::FRUIT_JUICE()));

        $this->cocktailIngredientOrchestrator->insertCocktailIngredient(new CocktailIngredient(
            $cocktailId2,
            $ingredient->getId(),
            1,
            250,
            'ml'
        ));

        $this->instructionOrchestrator->insertInstruction(new Instruction(
            $cocktailId2,
            1,
            'Drink you pussy'
        ));
    }
}