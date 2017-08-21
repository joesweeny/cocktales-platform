<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Ingredient;

use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
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

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(IngredientOrchestrator::class);
    }

    public function test_success_response_is_received_with_ingredients_details()
    {
        $this->createIngredients();

        $request = new ServerRequest('GET', '/api/v1/ingredient/all-by-category');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $jsend->status);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $jsend->data->allIngredientsByCategory->MIXER[0]->id);
        $this->assertEquals('Bacardi Breezer', $jsend->data->allIngredientsByCategory->MIXER[0]->name);
        $this->assertEquals('Mixer', $jsend->data->allIngredientsByCategory->MIXER[0]->category);
        $this->assertEquals('Alcopop', $jsend->data->allIngredientsByCategory->MIXER[0]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $jsend->data->allIngredientsByCategory->SPIRIT[0]->id);
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

        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName('Bacardi Breezer')
            ->setCategory(Category::MIXER())
            ->setType(Type::ALCOPOP()));

        $this->orchestrator->insertIngredient((new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
            ->setName("Gordon's Gin")
            ->setCategory(Category::SPIRIT())
            ->setType(Type::GIN()));
    }
}
