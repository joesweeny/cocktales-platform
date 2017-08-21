<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByTypeCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class GetIngredientsSortedByTypeCommandHandlerTest extends TestCase
{
    public function test_handle_returns_an_array_of_std_objects_containing_ingredient_information_sorted_by_type()
    {
        /** @var IngredientOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(IngredientOrchestrator::class);
        /** @var IngredientPresenter $presenter */
        $presenter = new IngredientPresenter;
        $handler = new GetIngredientsSortedByTypeCommandHandler($orchestrator->reveal(), $presenter);

        foreach ($this->allTypesExceptVodkaAndFruitJuice() as $key => $value) {
            $orchestrator->getIngredientsByType(new Type($value))->shouldBeCalled()->willReturn(new Collection([]));
        }

        $orchestrator->getIngredientsByType(Type::VODKA())->willReturn(new Collection([
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA()),
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        ]));

        $orchestrator->getIngredientsByType(Type::FRUIT_JUICE())->willReturn(new Collection([
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Cranberry Juice')
                ->setCategory(Category::MIXER())
                ->setType(Type::FRUIT_JUICE()),
        ]));

        $data = $handler->handle(new GetIngredientsSortedByTypeCommand);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['VODKA'][0]->id);
        $this->assertEquals('Smirnoff Black', $data['VODKA'][0]->name);
        $this->assertEquals('Spirit', $data['VODKA'][0]->category);
        $this->assertEquals('Vodka', $data['VODKA'][0]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['VODKA'][1]->id);
        $this->assertEquals('Smirnoff Red', $data['VODKA'][1]->name);
        $this->assertEquals('Spirit', $data['VODKA'][1]->category);
        $this->assertEquals('Vodka', $data['VODKA'][1]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['FRUIT_JUICE'][0]->id);
        $this->assertEquals('Cranberry Juice', $data['FRUIT_JUICE'][0]->name);
        $this->assertEquals('Mixer', $data['FRUIT_JUICE'][0]->category);
        $this->assertEquals('Fruit Juice', $data['FRUIT_JUICE'][0]->type);

        foreach ($this->allTypesExceptVodkaAndFruitJuice() as $key => $value) {
            $this->assertEmpty($data[$key]);
        }
    }

    private function allTypesExceptVodkaAndFruitJuice()
    {
        return array_diff(Type::toArray(), [Type::VODKA(), Type::FRUIT_JUICE()]);
    }
}
