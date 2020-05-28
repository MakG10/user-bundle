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
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UpdateUserCommand extends Command
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
            ->setName('makg:user:update')
            ->setDescription('Updates user with given data.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addOption('regenerate-avatar', 'g', InputOption::VALUE_NONE, 'Generate random avatar')
            ->addOption('activate', 'a', InputOption::VALUE_NONE, 'Activate user')
            ->addOption('deactivate', 'd', InputOption::VALUE_NONE, 'Deactivate user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
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


        if ($input->getOption('regenerate-avatar')) {
            $this->userManipulator->randomizeAvatar($user);

            $output->writeln(sprintf('Generated random avatar for user <comment>%s</comment>', $user->getEmail()));
        }

        if ($input->getOption('activate')) {
            $user->setEnabled(true);

            $output->writeln(sprintf('Activated user <comment>%s</comment>', $user->getEmail()));
        }

        if ($input->getOption('deactivate')) {
            $user->setEnabled(false);

            $output->writeln(sprintf('Deactivated user <comment>%s</comment>', $user->getEmail()));
        }

        $this->userManager->updateUser($user);

        return 0;
    }
}
