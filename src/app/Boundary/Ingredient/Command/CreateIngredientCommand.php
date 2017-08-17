<?php

namespace Cocktales\Boundary\Ingredient\Command;

use Cocktales\Domain\Ingredient\Enum\Category;
use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\CommandBus\Command;

class CreateIngredientCommand implements Command
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Category
     */
    private $category;
    /**
     * @var Type
     */
    private $type;

    /**
     * CreateIngredientCommand constructor.
     * @param string $name
     * @param string $category
     * @param string $type
     * @throws \UnexpectedValueException
     */
    public function __construct(string $name, string $category, string $type)
    {
        $this->name = $name;
        $this->category = new Category($category);
        $this->type = new Type($type);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getType(): Type
    {
        return $this->type;
    }
}
