<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\Ingredient\Command\CreateIngredientCommand;
use Cocktales\Domain\Ingredient\Exception\IngredientRepositoryException;
use Cocktales\Framework\CommandBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class IngredientCreate extends Command
{
    /** @var CommandBus */
    private $bus;

    /**
     * IngredientCreate constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('ingredient:create')
            ->setDescription('Create an ingredient')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('category', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $this->bus->execute(new CreateIngredientCommand(
                $input->getArgument('name'),
                $input->getArgument('category'),
                $input->getArgument('type')
            ));

            $response->success('Ingredient created!');
        } catch (IngredientRepositoryException $e) {
            $response->error($e->getMessage());
        }
    }
}
