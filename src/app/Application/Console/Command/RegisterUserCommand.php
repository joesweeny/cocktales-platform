<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Service\User\Command\RegisterUserCommand as RegisterUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterUserCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * RegisterUserCommand constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('user:register')
            ->setDescription('Register a new user')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('password_confirm', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('password') !== $input->getArgument('password_confirm')) {
            $output->writeln('Passwords do not match');
        }

        $this->bus->execute(
            new RegisterUser((object) [
                'email' => $input->getArgument('email'),
                'password' => $input->getArgument('password')
            ])
        );

        $output->writeln('User Registered!');
    }
}
