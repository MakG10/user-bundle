<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\UpdateUserCommand;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use MakG\UserBundle\Tests\CommandTestCase;
use MakG\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UpdateUserCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $user = new TestUser();
        $user->setEnabled(false);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManipulator = $this->createMock(UserManipulatorInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new UpdateUserCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:update', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                '--regenerate-avatar' => true,
                '--activate' => true,
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('avatar', $output);
        $this->assertStringContainsString('Activated', $output);
        $this->assertSame(true, $user->getEnabled());
    }

    public function testInvalidUserProvider()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only classes implementing');

        $user = new class
        {
        };

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new UpdateUserCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:update', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                '--activate' => true,
            ]
        );
    }
}
