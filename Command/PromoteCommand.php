<?php

namespace MakG\UserBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Alias command to match FOSUserBundle interface for better ux.
 * It runs RolesCommand with proper options.
 */
class PromoteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('makg:user:promote')
            ->setDescription('Assigns roles to user.')
            ->addArgument('email', InputArgument::REQUIRED, 'Valid e-mail address')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'List of roles');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = $this->getApplication()->find('makg:user:roles');
        $commandInput = new ArrayInput(
            [
                'command' => $command->getName(),
                'email' => $input->getArgument('email'),
                '--append' => $input->getArgument('roles'),
            ]
        );

        return $command->run($commandInput, $output);
    }
}
