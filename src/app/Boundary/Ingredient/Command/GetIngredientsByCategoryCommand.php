<?php

namespace Cocktales\Boundary\Ingredient\Command;

use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Framework\CommandBus\Command;

class GetIngredientsByCategoryCommand implements Command
{
    /**
     * @var Category
     */
    private $category;

    /**
     * GetIngredientsByCategoryCommand constructor.
     * @param string $category
     * @throws \UnexpectedValueException
     */
    public function __construct(string $category)
    {
        $this->category = new Category($category);
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
