<?php

namespace Cocktales\Application\Console\Command;

use Cocktales\Boundary\User\Command\RegisterUserCommand;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\Exception\UsernameValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
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
            ->setDescription('Register a new User');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @return string|array
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = new SymfonyStyle($input, $output);

        $email = $this->getEmail($input, $output);

        $password = $this->getPassword($input, $output);

        if ($password !== $this->getPasswordConfirmation($input, $output)) {
            $response->error('Passwords do not match. Please try again');
            return;
        }

        try {
            $this->bus->execute(new RegisterUserCommand((object) [
                'email' => $email,
                'password' => $password
            ]));

            $response->success('User Registered!');
        } catch (UsernameValidationException $e) {
            $response->error($e->getMessage());
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function getEmail(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question("Please enter the user's email \n");

        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function getPassword(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question("Please enter the user's password \n");
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return $helper->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private function getPasswordConfirmation(InputInterface $input, OutputInterface $output): string
    {
        $helper = $this->getHelper('question');

        $question = new Question("Please retype the password again \n");
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return $helper->ask($input, $output, $question);
    }
}
