<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\DemoteCommand;
use MakG\UserBundle\Command\RolesCommand;
use MakG\UserBundle\Tests\CommandTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * DemoteCommand is an alias to RolesCommand.
 */
class DemoteCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $command = new DemoteCommand();
        $rolesCommand = new class extends RolesCommand
        {
            public function __construct($name = null)
            {
                Command::__construct($name);
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $output->writeln('Roles Command executed.');

                return 0;
            }
        };

        $application = new Application();
        $application->add($command);
        $application->add($rolesCommand);


        $commandTester = new CommandTester($application->find($command->getName()));
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                'roles' => ['ROLE1', 'ROLE2'],
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Roles Command executed.', $output);
    }
}
