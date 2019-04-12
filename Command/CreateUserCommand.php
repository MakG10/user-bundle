<?php

namespace MakG\UserBundle\Command;


use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    private $userManager;
    private $userManipulator;

    public function __construct(UserManagerInterface $userManager, UserManipulatorInterface $userManipulator)
    {
        parent::__construct();

        $this->userManager = $userManager;
        $this->userManipulator = $userManipulator;
    }

    protected function configure()
    {
        $this
            ->setName('makg:user:create')
            ->setDescription('Creates a new user with random password.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addOption('inactive', 'i', InputOption::VALUE_NONE, 'Set the user as inactive')
            ->addOption(
                'send-confirmation-email',
                'c',
                InputOption::VALUE_NONE,
                'Send an e-mail with confirmation link'
            )
            ->addOption(
                'send-resetting-email',
                'e',
                InputOption::VALUE_NONE,
                'Send an e-mail with password reset link'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $enabled = !$input->getOption('inactive');

        $user = $this->userManager->createUser();
        $user->setEmail($email);
        $user->setEnabled($enabled);

        // Generate random password which won't be revealed
        $this->userManipulator->generateRandomPassword($user);

        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $user->getEmail()));
    }
}