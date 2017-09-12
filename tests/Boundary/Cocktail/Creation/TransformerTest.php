<?php

namespace Cocktales\Boundary\Cocktail\Creation;

use Cocktales\Domain\Cocktail\Entity\Cocktail;
use Cocktales\Domain\CocktailIngredient\Entity\CocktailIngredient;
use Cocktales\Domain\Instruction\Entity\Instruction;
use Cocktales\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    /** @var  Transformer */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new Transformer;
    }

    public function test_toCocktailIngredient_returns_an_ingredient_object_with_properties_set()
    {
        $ingredient = $this->transformer->toCocktailIngredient((object) [
            'ingredientId' => '73f261d9-234e-4501-a5dc-8f4f0bc0623a',
            'orderNumber' => 1,
            'quantity' => 50,
            'measurement' => 'ml'
        ], new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'));

        $this->assertInstanceOf(CocktailIngredient::class, $ingredient);
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $ingredient->getCocktailId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $ingredient->getIngredientId());
        $this->assertEquals(1, $ingredient->getOrderNumber());
        $this->assertEquals(50, $ingredient->getQuantity());
        $this->assertEquals('ml', $ingredient->getMeasurement());
    }

    public function test_toCocktailInstruction_returns_an_instruction_object_with_properties_set()
    {
        $instruction = $this->transformer->toCocktailInstruction((object) [
            'orderNumber' => 4,
            'text' => 'Shake well'
        ], new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'));

        $this->assertInstanceOf(Instruction::class, $instruction);
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $instruction->getCocktailId());
        $this->assertEquals(4, $instruction->getOrderNumber());
        $this->assertEquals('Shake well', $instruction->getText());
    }

    public function test_toCocktail_returns_a_cocktail_object_with_properties_set()
    {
        $cocktail = $this->transformer->toCocktail((object) [
            'id' => 'fe8f3ec8-1711-412c-8324-c1e1e5f19454',
            'userId' => '73f261d9-234e-4501-a5dc-8f4f0bc0623a',
            'name' => 'Smoking Joe',
            'origin' => 'Straight outta compton'
        ]);

        $this->assertInstanceOf(Cocktail::class, $cocktail);
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $cocktail->getId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $cocktail->getUserId());
        $this->assertEquals('Smoking Joe', $cocktail->getName());
        $this->assertEquals('Straight outta compton', $cocktail->getOrigin());
    }
}
