<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsSortedByCategoryCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class GetIngredientsSortedByCategoryCommandHandlerTest extends TestCase
{
    public function test_handle_returns_an_array_of_std_objects_containing_ingredient_information_sorted_by_category()
    {
        /** @var IngredientOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(IngredientOrchestrator::class);
        /** @var IngredientPresenter $presenter */
        $presenter = new IngredientPresenter;
        $handler = new GetIngredientsSortedByCategoryCommandHandler($orchestrator->reveal(), $presenter);

        foreach ($this->allCategoriesExceptSpiritAndMixer() as $key => $value) {
            $orchestrator->getIngredientsByCategory(new Category($value))->shouldBeCalled()->willReturn(new Collection([]));
        }

        $orchestrator->getIngredientsByCategory(Category::SPIRIT())->willReturn(new Collection([
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Smirnoff Black')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA()),
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        ]));

        $orchestrator->getIngredientsByCategory(Category::MIXER())->willReturn(new Collection([
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Cranberry Juice')
                ->setCategory(Category::MIXER())
                ->setType(Type::FRUIT_JUICE()),
        ]));

        $data = $handler->handle(new GetIngredientsSortedByCategoryCommand);
        
        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['SPIRIT'][0]->id);
        $this->assertEquals('Smirnoff Black', $data['SPIRIT'][0]->name);
        $this->assertEquals('Spirit', $data['SPIRIT'][0]->category);
        $this->assertEquals('Vodka', $data['SPIRIT'][0]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['SPIRIT'][1]->id);
        $this->assertEquals('Smirnoff Red', $data['SPIRIT'][1]->name);
        $this->assertEquals('Spirit', $data['SPIRIT'][1]->category);
        $this->assertEquals('Vodka', $data['SPIRIT'][1]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data['MIXER'][0]->id);
        $this->assertEquals('Cranberry Juice', $data['MIXER'][0]->name);
        $this->assertEquals('Mixer', $data['MIXER'][0]->category);
        $this->assertEquals('Fruit Juice', $data['MIXER'][0]->type);

        foreach ($this->allCategoriesExceptSpiritAndMixer() as $key => $value) {
            $this->assertEmpty($data[$key]);
        }
    }

    private function allCategoriesExceptSpiritAndMixer()
    {
        return array_diff(Category::toArray(), [Category::MIXER(), Category::SPIRIT()]);
    }
}
