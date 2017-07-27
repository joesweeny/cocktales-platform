<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\Profile\Command\GetProfileByUserIdCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserProfile extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * UserProfile constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('user:profile')
            ->setDescription('Display profile information for a registered user')
            ->addArgument('user_id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);


        try {
            $profile = $this->bus->execute(new GetProfileByUserIdCommand($input->getArgument('user_id')));

            $table = new Table($output);
            $table->setHeaders(array_keys((array) $profile));

            $table->addRow((array) $profile);

            $table->render();
        } catch (NotFoundException $e) {
            $response->error($e->getMessage());
        }
    }
}
