<?php

namespace Cocktales\Boundary\Ingredient\Command\Handlers;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsByCategoryCommand;
use Cocktales\Boundary\Ingredient\IngredientPresenter;
use Cocktales\Domain\Ingredient\Entity\Ingredient;
use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Domain\Ingredient\IngredientOrchestrator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class GetIngredientsByCategoryCommandHandlerTest extends TestCase
{
    public function test_handle_returns_an_array_of_std_objects_containing_ingredients_information_based_on_the_same_category()
    {
        /** @var IngredientOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(IngredientOrchestrator::class);
        /** @var IngredientPresenter $presenter */
        $presenter = new IngredientPresenter;
        $handler = new GetIngredientsByCategoryCommandHandler($orchestrator->reveal(), $presenter);

        $command = new GetIngredientsByCategoryCommand('Wine');

        $orchestrator->getIngredientsByCategory($command->getCategory())->willReturn(new Collection([
            (new Ingredient('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5'))
                ->setName('Barefoot')
                ->setCategory(Category::WINE())
                ->setType(Type::ROSE_WINE()),
            (new Ingredient('c9b9d751-8b5c-4a5f-93e4-492ef526e704'))
                ->setName('Black Tower')
                ->setCategory(Category::WINE())
                ->setType(Type::ROSE_WINE()),
            (new Ingredient('bc49654e-5e0a-4f65-ae32-483365b9c838'))
                ->setName('Echo Falls White Zinfandel')
                ->setCategory(Category::WINE())
                ->setType(Type::ROSE_WINE())
        ]));

        $ingredients = $handler->handle($command);

        $this->assertEquals('e6885733-72b8-4ebe-bbb5-2cee7d6bd0a5', $ingredients[0]->id);
        $this->assertEquals('Barefoot', $ingredients[0]->name);
        $this->assertEquals('Wine', $ingredients[0]->category);
        $this->assertEquals('Rose Wine', $ingredients[0]->type);

        $this->assertEquals('c9b9d751-8b5c-4a5f-93e4-492ef526e704', $ingredients[1]->id);
        $this->assertEquals('Black Tower', $ingredients[1]->name);
        $this->assertEquals('Wine', $ingredients[1]->category);
        $this->assertEquals('Rose Wine', $ingredients[1]->type);

        $this->assertEquals('bc49654e-5e0a-4f65-ae32-483365b9c838', $ingredients[2]->id);
        $this->assertEquals('Echo Falls White Zinfandel', $ingredients[2]->name);
        $this->assertEquals('Wine', $ingredients[2]->category);
        $this->assertEquals('Rose Wine', $ingredients[2]->type);
    }
}
