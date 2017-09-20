<?php

namespace Cocktales\Domain\Cocktail\Creation;

use Cocktales\Domain\Cocktail\CocktailOrchestrator;
use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\Cocktail\Exception\DuplicateNameException;
use Cocktales\Domain\CocktailIngredient\CocktailIngredientOrchestrator;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Domain\Instruction\InstructionOrchestrator;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class BartenderTest extends TestCase
{
    /** @var  CocktailOrchestrator */
    private $cocktails;
    /** @var  CocktailIngredientOrchestrator */
    private $ingredients;
    /** @var  InstructionOrchestrator */
    private $instructions;
    /** @var  Bartender */
    private $bartender;

    public function setUp()
    {
        $this->cocktails = $this->prophesize(CocktailOrchestrator::class);
        $this->ingredients = $this->prophesize(CocktailIngredientOrchestrator::class);
        $this->instructions = $this->prophesize(InstructionOrchestrator::class);
        $this->bartender = new Bartender(
            $this->cocktails->reveal(),
            $this->ingredients->reveal(),
            $this->instructions->reveal()
        );
    }

    public function test_create_saves_cocktail_ingredients_and_instructions_to_the_database()
    {
        $cocktail = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed');

        $ingredients = new Collection([
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
                1,
                2,
                'oz'
            )
        ]);

        $instructions = new Collection([
            $instruction1 = new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                1,
                'Pour into glass'
            ),
            $instruction2 = new Instruction(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                1,
                'Pour into glass'
            )
        ]);

        $cocktail->setIngredients($ingredients)->setInstructions($instructions);

        $this->cocktails->canCreateCocktail($cocktail)->willReturn(true);

        $this->cocktails->createCocktail($cocktail)->shouldBeCalled();

        $this->ingredients->insertCocktailIngredient($ingredient1)->shouldBeCalled();
        $this->ingredients->insertCocktailIngredient($ingredient2)->shouldBeCalled();

        $this->instructions->insertInstruction($instruction1)->shouldBeCalled();
        $this->instructions->insertInstruction($instruction2)->shouldBeCalled();

        $this->bartender->create($cocktail);
    }

    public function test_exception_is_thrown_if_cocktail_name_is_already_taken()
    {
        $cocktail = (new Cocktail(
            new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
            new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
            'The Titty Twister'
        ))->setOrigin('Made in my garage when pissed');

        $this->cocktails->canCreateCocktail($cocktail)->willReturn(false);

        $this->ingredients->insertCocktailIngredient(Argument::type(CocktailIngredient::class))->shouldNotBeCalled();
        $this->instructions->insertInstruction(Argument::type(Instruction::class))->shouldNotBeCalled();

        $this->expectException(DuplicateNameException::class);
        $this->bartender->create($cocktail);
    }

    public function test_serve_cocktail_returns_a_cocktail_object_containing_cocktail_details_ingredients_and_instructions()
    {
        $this->cocktails->getCocktailById($cocktailId = new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'))->willReturn(
            $cocktail = (new Cocktail(
                new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                new Uuid('f5a366cf-15a0-4aca-a19e-e77c3e71815f'),
                'The Titty Twister'
            ))->setOrigin('Made in my garage when pissed')
        );

        $this->ingredients->getCocktailIngredients($cocktailId)->willReturn(
            $ingredients = new Collection([
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
                    1,
                    2,
                    'oz'
                )
            ])
        );

        $this->instructions->getInstructions($cocktailId)->willReturn(
            $instructions = new Collection([
                $instruction1 = new Instruction(
                    new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                    1,
                    'Pour into glass'
                ),
                $instruction2 = new Instruction(
                    new Uuid('0487d724-4ca0-4942-bf64-4cc53273bc2b'),
                    1,
                    'Pour into glass'
                )
            ])
        );

        $cocktail = $this->bartender->serveCocktail($cocktailId);

        $this->assertInstanceOf(Cocktail::class, $cocktail);
        $this->assertEquals('The Titty Twister', $cocktail->getName());
        $this->assertEquals('f5a366cf-15a0-4aca-a19e-e77c3e71815f', (string) $cocktail->getUserId());
        $this->assertEquals('Made in my garage when pissed', $cocktail->getOrigin());

        foreach ($cocktail->getIngredients() as $ingredient) {
            $this->assertInstanceOf(CocktailIngredient::class, $ingredient);
            $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $ingredient->getCocktailId());
        }

        foreach ($cocktail->getInstructions() as $instruction) {
            $this->assertInstanceOf(Instruction::class, $instruction);
            $this->assertEquals('0487d724-4ca0-4942-bf64-4cc53273bc2b', $instruction->getCocktailId());
        }
    }
}
