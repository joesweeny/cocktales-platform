<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\User\Command\CreateUserCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\UsernameValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserRegister extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * UserRegister constructor.
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
            ->setDescription('Register a new User')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $this->bus->execute(new CreateUserCommand((object) [
                'email' => $input->getArgument('email'),
                'password' => $input->getArgument('password')
            ]));

            $response->success('User Registered!');
        } catch (UsernameValidationException $e) {
            $response->error($e->getMessage());
        }
    }
}
