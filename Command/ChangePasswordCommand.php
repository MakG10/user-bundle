<?php

namespace MakG\UserBundle\Command;


use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ChangePasswordCommand extends Command
{
    private $userManager;
    private $userManipulator;
    private $userProvider;

    public function __construct(
        UserManagerInterface $userManager,
        UserManipulatorInterface $userManipulator,
        UserProviderInterface $userProvider
    ) {
        parent::__construct();

        $this->userManager = $userManager;
        $this->userManipulator = $userManipulator;
        $this->userProvider = $userProvider;
    }

    protected function configure()
    {
        $this
            ->setName('makg:user:change-password')
            ->setDescription('Changes the password of a user to specified or random.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addArgument('password', InputArgument::OPTIONAL, 'New password')
            ->addOption('random', 'r', InputOption::VALUE_NONE, 'Generate random password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $generateRandomPassword = $input->getOption('random');


        $user = $this->userProvider->loadUserByUsername($email);

        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Only classes implementing "%s" interface are supported. "%s" given.',
                    UserInterface::class,
                    $user ? get_class($user) : 'null'
                )
            );
        }

        if ($generateRandomPassword) {
            $this->userManipulator->generateRandomPassword($user);
        } elseif ($password) {
            $user->setPlainPassword($password);
        } else {
            $output->writeln('You must provide a password.');

            return 1;
        }

        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Changed password for user <comment>%s</comment>', $user->getEmail()));

        return 0;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('password') && !$input->getOption('random')) {
            $question = new Question('Please enter the new password. Leave blank for random password:');
            $question->setHidden(true);

            $answer = $this->getHelper('question')->ask($input, $output, $question);

            if (empty($answer)) {
                $input->setOption('random', true);
            } else {
                $input->setArgument('password', $answer);
            }
        }
    }
}
