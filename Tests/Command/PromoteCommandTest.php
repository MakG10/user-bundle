<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\PromoteCommand;
use MakG\UserBundle\Command\RolesCommand;
use MakG\UserBundle\Tests\CommandTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * PromoteCommand is an alias to RolesCommand.
 */
class PromoteCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $command = new PromoteCommand();
        $rolesCommand = new class extends RolesCommand
        {
            public function __construct($name = null)
            {
                Command::__construct($name);
            }

            protected function execute(InputInterface $input, OutputInterface $output)
            {
                $output->writeln('Roles Command executed.');
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
