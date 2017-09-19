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
        $ingredients = [
            (object) [
                'ingredientId' => '73f261d9-234e-4501-a5dc-8f4f0bc0623a',
                'orderNumber' => 1,
                'quantity' => 50,
                'measurement' => 'ml'
            ],
            (object) [
                'ingredientId' => '2ad51a5e-3b33-40f9-9143-ea261531ba2d',
                'orderNumber' => 2,
                'quantity' => 10,
                'measurement' => 'oz'
            ]
        ];

        $collection = $this->transformer->toCocktailIngredients($ingredients, new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'));

        $this->assertInstanceOf(CocktailIngredient::class, $collection->first());
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $collection->first()->getCocktailId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $collection->first()->getIngredientId());
        $this->assertEquals(1, $collection->first()->getOrderNumber());
        $this->assertEquals(50, $collection->first()->getQuantity());
        $this->assertEquals('ml', $collection->first()->getMeasurement());

        $this->assertInstanceOf(CocktailIngredient::class, $collection->last());
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $collection->last()->getCocktailId());
        $this->assertEquals('2ad51a5e-3b33-40f9-9143-ea261531ba2d', (string) $collection->last()->getIngredientId());
        $this->assertEquals(2, $collection->last()->getOrderNumber());
        $this->assertEquals(10, $collection->last()->getQuantity());
        $this->assertEquals('oz', $collection->last()->getMeasurement());
    }

    public function test_toCocktailInstruction_returns_an_instruction_object_with_properties_set()
    {
        $instructions = [
            (object) [
                'orderNumber' => 4,
                'text' => 'Shake well'
            ],
            (object) [
                'orderNumber' => 5,
                'text' => 'Pour'
            ]
        ];

        $collection = $this->transformer->toCocktailInstructions($instructions, new Uuid('fe8f3ec8-1711-412c-8324-c1e1e5f19454'));

        $this->assertInstanceOf(Instruction::class, $collection->first());
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $collection->first()->getCocktailId());
        $this->assertEquals(4, $collection->first()->getOrderNumber());
        $this->assertEquals('Shake well', $collection->first()->getText());

        $this->assertInstanceOf(Instruction::class, $collection->last());
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $collection->last()->getCocktailId());
        $this->assertEquals(5, $collection->last()->getOrderNumber());
        $this->assertEquals('Pour', $collection->last()->getText());
    }

    public function test_toCocktail_returns_a_cocktail_object_with_properties_set()
    {
        $cocktail = $this->transformer->toCocktail((object) [
            'id' => 'fe8f3ec8-1711-412c-8324-c1e1e5f19454',
            'name' => 'Smoking Joe',
            'origin' => 'Straight outta compton'
        ], new Uuid('73f261d9-234e-4501-a5dc-8f4f0bc0623a'));

        $this->assertInstanceOf(Cocktail::class, $cocktail);
        $this->assertEquals('fe8f3ec8-1711-412c-8324-c1e1e5f19454', (string) $cocktail->getId());
        $this->assertEquals('73f261d9-234e-4501-a5dc-8f4f0bc0623a', (string) $cocktail->getUserId());
        $this->assertEquals('Smoking Joe', $cocktail->getName());
        $this->assertEquals('Straight outta compton', $cocktail->getOrigin());
    }
}
