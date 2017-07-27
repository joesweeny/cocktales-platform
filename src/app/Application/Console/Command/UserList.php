<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\User\Command\ListUsersCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserList extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * UserList constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('user:list')
            ->setDescription('List all registered users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->bus->execute(new ListUsersCommand);

        $table = new Table($output);
        $table->setHeaders(array_keys((array) $users[0]));

        foreach ($users as $user) {
            $table->addRow((array) $user);
        }

        $table->render();
    }
}
