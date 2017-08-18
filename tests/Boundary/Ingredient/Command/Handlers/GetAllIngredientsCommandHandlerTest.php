<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetAllIngredientsCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class GetAllIngredientsCommandHandlerTest extends TestCase
{
    public function test_handle_returns_an_array_of_std_classes_containing_ingredient_information()
    {
        /** @var IngredientOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(IngredientOrchestrator::class);
        /** @var IngredientPresenter $presenter */
        $presenter = new IngredientPresenter;
        $handler = new GetAllIngredientsCommandHandler($orchestrator->reveal(), $presenter);

        $orchestrator->getIngredients()->willReturn(new Collection([
            $ingredient1 = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Bacardi Breezer')
                ->setCategory(Category::MIXER())
                ->setType(Type::ALCOPOP()),
            $ingredient2 = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Orange Juice')
                ->setCategory(Category::MIXER())
                ->setType(Type::FRUIT_JUICE()),
            $ingredient3 = (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Smirnoff Red')
                ->setCategory(Category::SPIRIT())
                ->setType(Type::VODKA())
        ]));

        $data = $handler->handle(new GetAllIngredientsCommand);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data[0]->id);
        $this->assertEquals('Bacardi Breezer', $data[0]->name);
        $this->assertEquals('Mixer', $data[0]->category);
        $this->assertEquals('Alcopop', $data[0]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data[1]->id);
        $this->assertEquals('Orange Juice', $data[1]->name);
        $this->assertEquals('Mixer', $data[1]->category);
        $this->assertEquals('Fruit Juice', $data[1]->type);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $data[2]->id);
        $this->assertEquals('Smirnoff Red', $data[2]->name);
        $this->assertEquals('Spirit', $data[2]->category);
        $this->assertEquals('Vodka', $data[2]->type);
    }
}
