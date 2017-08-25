<?php

namespace Cocktales\Boundary\Ingredient\Command;

use Cocktales\Domain\Ingredient\Enum\Type;
use Cocktales\Framework\CommandBus\Command;

class GetIngredientsByTypeCommand implements Command
{
    /**
     * @var Type
     */
    private $type;

    /**
     * GetIngredientsByTypeCommand constructor.
     * @param string $type
     * @throws \UnexpectedValueException
     */
    public function __construct(string $type)
    {
        $this->type = new Type($type);
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }
}
