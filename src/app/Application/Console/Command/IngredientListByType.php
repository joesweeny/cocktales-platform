<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\Ingredient\Command\GetIngredientsByTypeCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IngredientListByType extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * IngredientListByType constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('ingredient:list-by-type')
            ->setDescription('List Ingredients by a specific Type')
            ->addArgument('type', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $ingredients = $this->bus->execute(new GetIngredientsByTypeCommand($input->getArgument('type')));

            $table = new Table($output);
            $table->setHeaders(array_keys((array) $ingredients[0]));

            foreach ($ingredients as $ingredient) {
                $table->addRow((array) $ingredient);
            }

            $table->render();
        } catch (\Exception $e) {
            $response->error($e->getMessage());
        }
    }
}
