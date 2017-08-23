<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\Ingredient\Command\GetAllIngredientsCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IngredientList extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * IngredientList constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('ingredient:list')
            ->setDescription('List all ingredients in alphabetical order');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ingredients = $this->bus->execute(new GetAllIngredientsCommand);

        $table = new Table($output);
        $table->setHeaders(array_keys((array) $ingredients[0]));

        foreach ($ingredients as $ingredient) {
            $table->addRow((array) $ingredient);
        }

        $table->render();
    }
}
