<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
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
use PHPUnit\Framework\TestCase;

class GetAllByCategoryControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  IngredientOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(IngredientOrchestrator::class);
        $this->user = $this->container->get(UserOrchestrator::class)->createUser(
            (new User('f530caab-1767-4f0c-a669-331a7bf0fc85'))->setEmail('joe@joe.com')->setPasswordHash(new PasswordHash('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_with_ingredients_details()
    {
        $this->createIngredients();

        $request = new ServerRequest(
            'GET',
            '/api/v1/ingredient/all-by-category',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()]
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $jsend->status);

        $this->assertEquals('6a56edad-530f-4b75-9389-050e2aa3c34a', $jsend->data->allIngredientsByCategory->MIXER[0]->id);
        $this->assertEquals('Bacardi Breezer', $jsend->data->allIngredientsByCategory->MIXER[0]->name);
        $this->assertEquals('Mixer', $jsend->data->allIngredientsByCategory->MIXER[0]->category);
        $this->assertEquals('Alcopop', $jsend->data->allIngredientsByCategory->MIXER[0]->type);

        $this->assertEquals('672f0f74-190a-4e73-ba82-84f6aed308ad', $jsend->data->allIngredientsByCategory->SPIRIT[0]->id);
        $this->assertEquals("Gordon's Gin", $jsend->data->allIngredientsByCategory->SPIRIT[0]->name);
        $this->assertEquals('Spirit', $jsend->data->allIngredientsByCategory->SPIRIT[0]->category);
        $this->assertEquals('Gin', $jsend->data->allIngredientsByCategory->SPIRIT[0]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $jsend->data->allIngredientsByCategory->SPIRIT[1]->id);
        $this->assertEquals('Smirnoff Red', $jsend->data->allIngredientsByCategory->SPIRIT[1]->name);
        $this->assertEquals('Spirit', $jsend->data->allIngredientsByCategory->SPIRIT[1]->category);
        $this->assertEquals('Vodka', $jsend->data->allIngredientsByCategory->SPIRIT[1]->type);
    }

    private function createIngredients()
    {
        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Smirnoff Red')
            ->setCategory(Category::SPIRIT())
            ->setType(Type::VODKA()));

        $this->orchestrator->insertIngredient((new Ingredient('6a56edad-530f-4b75-9389-050e2aa3c34a'))
            ->setName('Bacardi Breezer')
            ->setCategory(Category::MIXER())
            ->setType(Type::ALCOPOP()));

        $this->orchestrator->insertIngredient((new Ingredient('672f0f74-190a-4e73-ba82-84f6aed308ad'))
            ->setName("Gordon's Gin")
            ->setCategory(Category::SPIRIT())
            ->setType(Type::GIN()));
    }
}
