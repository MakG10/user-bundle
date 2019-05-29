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

class RolesCommand extends Command
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
            ->setName('makg:user:roles')
            ->setDescription('Assigns roles to user.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addOption(
                'append',
                'a',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'List of roles to add'
            )
            ->addOption(
                'delete',
                'd',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'List of roles to remove'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $rolesToAdd = $input->getOption('append');
        $rolesToRemove = $input->getOption('delete');


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


        foreach ($rolesToAdd as $role) {
            $this->userManipulator->addRole($user, $role);
        }

        foreach ($rolesToRemove as $role) {
            $this->userManipulator->removeRole($user, $role);
        }

        $this->userManager->updateUser($user);

        $output->writeln(sprintf('Roles set for user <comment>%s</comment>', $user->getEmail()));
    }
}