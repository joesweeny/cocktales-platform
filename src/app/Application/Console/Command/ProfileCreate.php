<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\Profile\Command\CreateProfileCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\UsernameValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProfileCreate extends Command
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
        $this->setName('profile:create')
            ->setDescription('Create a Profile for a registered User')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('first_name', InputArgument::OPTIONAL, '')
            ->addArgument('last_name', InputArgument::OPTIONAL, '')
            ->addArgument('location', InputArgument::OPTIONAL, '')
            ->addArgument('slogan', InputArgument::OPTIONAL, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        try {
            $this->bus->execute(new CreateProfileCommand((object) [
                'user_id' => $input->getArgument('user_id'),
                'username' => $input->getArgument('username'),
                'first_name' => $input->getArgument('first_name'),
                'last_name' => $input->getArgument('last_name'),
                'location' => $input->getArgument('location'),
                'slogan' => $input->getArgument('slogan'),
            ]));

            $response->success('Profile created!');
        } catch (UsernameValidationException $e) {
            $response->error($e->getMessage());
        }
    }
}
