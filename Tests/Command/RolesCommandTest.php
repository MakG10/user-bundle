<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\RolesCommand;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use MakG\UserBundle\Tests\CommandTestCase;
use MakG\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RolesCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $user = new TestUser();

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);
        $userManipulator
            ->expects($this->exactly(2))
            ->method('addRole')
            ->withConsecutive([$user, 'ROLE3'], [$user, 'ROLE4']);
        $userManipulator
            ->expects($this->once())
            ->method('removeRole')
            ->with($user, 'ROLE1');

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new RolesCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:roles', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                '-a' => ['ROLE3', 'ROLE4'],
                '-d' => ['ROLE1'],
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Roles set', $output);
    }
}
