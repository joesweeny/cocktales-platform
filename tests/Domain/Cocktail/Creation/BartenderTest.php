<?php

namespace Cocktales\Domain\Cocktail\Creation;

use Cocktales\Boundary\Cocktail\Serving\Bartender;
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
    public function test_serve_cocktail_returns_an_std_class_object_containing_full_cocktail_information()
    {
        $cocktailPresenter = new CocktailPresenter;
        $ingredientPresenter = new CocktailIngredientPresenter;
        $instructionPresenter = new InstructionPresenter;
        /** @var IngredientOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(IngredientOrchestrator::class);
        $bartender = new Bartender(
            $cocktailPresenter,
            $ingredientPresenter,
            $instructionPresenter,
            $orchestrator->reveal()
        );

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

        $orchestrator->getIngredientById($ingredient1->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $orchestrator->getIngredientById($ingredient2->getIngredientId())->willReturn(
            (new Ingredient('f5a366cf-15a0-4aca-a19e-e77c3e71815f'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        );

        $drink = $bartender->serveCocktail($cocktail);

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
}
