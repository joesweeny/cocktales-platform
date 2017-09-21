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

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->cocktailOrchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->ingredientOrchestrator = $this->container->get(IngredientOrchestrator::class);
        $this->cocktailIngredientOrchestrator = $this->container->get(CocktailIngredientOrchestrator::class);
        $this->instructionOrchestrator = $this->container->get(InstructionOrchestrator::class);
    }

    public function test_returns_success_response_and_cocktail_object()
    {
        $this->createCocktail();

        $request = new ServerRequest('GET', '/api/v1/cocktail/get-by-id', [], '{"cocktailId":"0487d724-4ca0-4942-bf64-4cc53273bc2b"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertInstanceOf(\stdClass::class, $jsend->data->cocktail);
    }

    public function test_returns_error_response_if_cocktail_does_not_exist()
    {
        $request = new ServerRequest('GET', '/api/v1/cocktail/get-by-id', [], '{"cocktailId":"0487d724-4ca0-4942-bf64-4cc53273bc2b"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('error', $jsend->status);
        $this->assertEquals('Cocktail does not exist', $jsend->data->error);
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
