<?php

namespace Cocktales\Domain\Cocktail\Creation;

use Cocktales\Boundary\Cocktail\Serving\Bartender;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Cocktail\CocktailPresenter;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientPresenter;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\InstructionPresenter;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class BartenderTest extends TestCase
{
    /** @var  CocktailPresenter */
    private $cocktailPresenter;
    /** @var  IngredientPresenter */
    private $ingredientPresenter;
    /** @var  InstructionPresenter */
    private $instructionPresenter;
    /** @var  IngredientOrchestrator */
    private $orchestrator;
    /** @var  Bartender */
    private $bartender;

    public function setUp()
    {
        $this->cocktailPresenter = new CocktailPresenter;
        $this->ingredientPresenter = new CocktailIngredientPresenter;
        $this->instructionPresenter = new InstructionPresenter;
        /** @var IngredientOrchestrator $orchestrator */
        $this->orchestrator = $this->prophesize(IngredientOrchestrator::class);
        $this->bartender = new Bartender(
            $this->cocktailPresenter,
            $this->ingredientPresenter,
            $this->instructionPresenter,
            $this->orchestrator->reveal()
        );
    }

    public function test_serve_cocktail_returns_an_std_class_object_containing_full_cocktail_information()
    {
        $cocktail = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed')->setCreatedDate(new \DateTimeImmutable('2017-03-12'));

        $cocktail->setIngredients(new Collection([
            $ingredient1 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
                1,
                50,
                'ml'
            ),
            $ingredient2 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                2,
                2,
                'oz'
            )
        ]));

        $cocktail->setInstructions(new Collection([
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                1,
                'Pour into glass'
            ),
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                2,
                'Drink'
            )
        ]));

        $this->orchestrator->getIngredientById($ingredient1->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $this->orchestrator->getIngredientById($ingredient2->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $drink = $this->bartender->serveCocktail($cocktail);

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $drink->cocktail->id);
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', $drink->cocktail->userId);
        $this->assertEquals('The Titty Twister', $drink->cocktail->name);
        $this->assertEquals('Made in my garage when pissed', $drink->cocktail->origin);
        $this->assertEquals('2017-03-12', $drink->cocktail->createdAt);

        $this->assertEquals('Smirnoff Red', $drink->ingredients[0]->name);
        $this->assertEquals(1, $drink->ingredients[0]->order_number);
        $this->assertEquals(50, $drink->ingredients[0]->quantity);
        $this->assertEquals('ml', $drink->ingredients[0]->measurement);

        $this->assertEquals('Smirnoff Black', $drink->ingredients[1]->name);
        $this->assertEquals(2, $drink->ingredients[1]->order_number);
        $this->assertEquals(2, $drink->ingredients[1]->quantity);
        $this->assertEquals('oz', $drink->ingredients[1]->measurement);

        $this->assertEquals(1, $drink->instructions[0]->number);
        $this->assertEquals('Pour into glass', $drink->instructions[0]->text);

        $this->assertEquals(2, $drink->instructions[1]->number);
        $this->assertEquals('Drink', $drink->instructions[1]->text);
    }
    
    public function test_serve_multiple_cocktails_returns_an_std_class_containing_information_for_multiple_cocktails()
    {
        $cocktail = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed')->setCreatedDate(new \DateTimeImmutable('2017-03-12'));

        $cocktail->setIngredients(new Collection([
            $ingredient1 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
                1,
                50,
                'ml'
            ),
            $ingredient2 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                2,
                2,
                'oz'
            )
        ]));

        $cocktail->setInstructions(new Collection([
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                1,
                'Pour into glass'
            ),
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                2,
                'Drink'
            )
        ]));

        $this->orchestrator->getIngredientById($ingredient1->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $this->orchestrator->getIngredientById($ingredient2->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $cocktail2 = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed')->setCreatedDate(new \DateTimeImmutable('2017-03-12'));

        $cocktail2->setIngredients(new Collection([
            $ingredient3 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'),
                1,
                50,
                'ml'
            ),
            $ingredient4 = new CocktailIngredient(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                2,
                2,
                'oz'
            )
        ]));

        $cocktail2->setInstructions(new Collection([
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                1,
                'Pour into glass'
            ),
            new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                2,
                'Drink'
            )
        ]));

        $this->orchestrator->getIngredientById($ingredient3->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $this->orchestrator->getIngredientById($ingredient4->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $cocktails = new Collection([
            $cocktail,
            $cocktail2
        ]);

        $drinks = $this->bartender->serveMultipleCocktails($cocktails);

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $drinks->cocktails[0]->cocktail->id);
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', $drinks->cocktails[0]->cocktail->userId);
        $this->assertEquals('The Titty Twister', $drinks->cocktails[0]->cocktail->name);
        $this->assertEquals('Made in my garage when pissed', $drinks->cocktails[0]->cocktail->origin);
        $this->assertEquals('2017-03-12', $drinks->cocktails[0]->cocktail->createdAt);

        $this->assertEquals('Smirnoff Red', $drinks->cocktails[0]->ingredients[0]->name);
        $this->assertEquals(1, $drinks->cocktails[0]->ingredients[0]->order_number);
        $this->assertEquals(50, $drinks->cocktails[0]->ingredients[0]->quantity);
        $this->assertEquals('ml', $drinks->cocktails[0]->ingredients[0]->measurement);

        $this->assertEquals('Smirnoff Black', $drinks->cocktails[0]->ingredients[1]->name);
        $this->assertEquals(2, $drinks->cocktails[0]->ingredients[1]->order_number);
        $this->assertEquals(2, $drinks->cocktails[0]->ingredients[1]->quantity);
        $this->assertEquals('oz', $drinks->cocktails[0]->ingredients[1]->measurement);

        $this->assertEquals(1, $drinks->cocktails[0]->instructions[0]->number);
        $this->assertEquals('Pour into glass', $drinks->cocktails[0]->instructions[0]->text);

        $this->assertEquals(2, $drinks->cocktails[0]->instructions[1]->number);
        $this->assertEquals('Drink', $drinks->cocktails[0]->instructions[1]->text);

        $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $drinks->cocktails[1]->cocktail->id);
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', $drinks->cocktails[1]->cocktail->userId);
        $this->assertEquals('The Titty Twister', $drinks->cocktails[1]->cocktail->name);
        $this->assertEquals('Made in my garage when pissed', $drinks->cocktails[1]->cocktail->origin);
        $this->assertEquals('2017-03-12', $drinks->cocktails[1]->cocktail->createdAt);

        $this->assertEquals('Smirnoff Red', $drinks->cocktails[1]->ingredients[0]->name);
        $this->assertEquals(1, $drinks->cocktails[1]->ingredients[0]->order_number);
        $this->assertEquals(50, $drinks->cocktails[1]->ingredients[0]->quantity);
        $this->assertEquals('ml', $drinks->cocktails[1]->ingredients[0]->measurement);

        $this->assertEquals('Smirnoff Black', $drinks->cocktails[1]->ingredients[1]->name);
        $this->assertEquals(2, $drinks->cocktails[1]->ingredients[1]->order_number);
        $this->assertEquals(2, $drinks->cocktails[1]->ingredients[1]->quantity);
        $this->assertEquals('oz', $drinks->cocktails[1]->ingredients[1]->measurement);

        $this->assertEquals(1, $drinks->cocktails[1]->instructions[0]->number);
        $this->assertEquals('Pour into glass', $drinks->cocktails[1]->instructions[0]->text);

        $this->assertEquals(2, $drinks->cocktails[1]->instructions[1]->number);
        $this->assertEquals('Drink', $drinks->cocktails[1]->instructions[1]->text);
    }
}
