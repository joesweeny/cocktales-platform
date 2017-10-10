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

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->cocktailOrchestrator = $this->container->get(CocktailOrchestrator::class);
        $this->ingredientOrchestrator = $this->container->get(IngredientOrchestrator::class);
        $this->cocktailIngredientOrchestrator = $this->container->get(CocktailIngredientOrchestrator::class);
        $this->instructionOrchestrator = $this->container->get(InstructionOrchestrator::class);
    }

    public function test_returns_success_response_with_multiple_cocktail_information()
    {
        $this->createCocktails();

        $request = new ServerRequest('GET', '/api/v1/cocktail/get-by-user', [], '{"userId":"f5a366cf-15a0-4aca-a19e-e77c3e71815f"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertInstanceOf(\stdClass::class, $jsend->data->cocktails);
        $this->assertNotEmpty($jsend->data->cocktails->cocktails);
    }

    public function test_returns_success_and_an_emtpy_array_if_user_does_not_have_cocktails_or_user_does_not_exist()
    {
        $request = new ServerRequest('GET', '/api/v1/cocktail/get-by-user', [], '{"userId":"f5a366cf-15a0-4aca-a19e-e77c3e71815f"}');

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEmpty($jsend->data->cocktails->cocktails);
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