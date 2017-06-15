<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Service\Profile\Command\UpdateProfileCommand as UpdateProfile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProfileCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * UpdateProfileCommand constructor.
     * @param CommandBus $bus
     */
    public function __construct(CommandBus $bus)
    {
        parent::__construct();
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this->setName('profile:update')
            ->setDescription('Update the profile of an existing user')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('first_name', InputArgument::REQUIRED)
            ->addArgument('last_name', InputArgument::REQUIRED)
            ->addArgument('city', InputArgument::REQUIRED)
            ->addArgument('county', InputArgument::REQUIRED)
            ->addArgument('slogan', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bus->execute(
            new UpdateProfile((object) [
                'user_id' => $input->getArgument('user_id'),
                'username' => $input->getArgument('username'),
                'first_name' => $input->getArgument('first_name'),
                'last_name' => $input->getArgument('last_name'),
                'city' => $input->getArgument('city'),
                'county' => $input->getArgument('county'),
                'slogan' => $input->getArgument('slogan')
            ])
        );

        $output->writeln('Profile updated!');
    }
}
