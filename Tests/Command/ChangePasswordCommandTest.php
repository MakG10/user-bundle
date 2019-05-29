<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\ChangePasswordCommand;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use MakG\UserBundle\Tests\CommandTestCase;
use MakG\UserBundle\Tests\TestUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ChangePasswordCommandTest extends CommandTestCase
{
    public function testChangeToSpecificPassword()
    {
        $user = new TestUser();
        $user->setEmail('makg@example.com');

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new ChangePasswordCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:change-password', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                'password' => 'test',
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Changed password for user makg@example.com', $output);
        $this->assertSame('test', $user->getPlainPassword());
    }

    public function testChangeToRandomPassword()
    {
        $user = new TestUser();
        $user->setEmail('makg@example.com');

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);
        $userManipulator
            ->expects($this->once())
            ->method('generateRandomPassword');

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new ChangePasswordCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:change-password', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                '--random' => true,
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Changed password for user makg@example.com', $output);
    }

    public function testChangeToSpecificPasswordInteractively()
    {
        $user = new TestUser();
        $user->setEmail('makg@example.com');

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new ChangePasswordCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:change-password', $command);
        $commandTester->setInputs(['test']);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Changed password for user makg@example.com', $output);
        $this->assertSame('test', $user->getPlainPassword());
    }

    public function testChangeToRandomPasswordInteractively()
    {
        $user = new TestUser();
        $user->setEmail('makg@example.com');

        $userManager = $this->createMock(UserManagerInterface::class);

        $userManipulator = $this->createMock(UserManipulatorInterface::class);
        $userManipulator
            ->expects($this->once())
            ->method('generateRandomPassword');

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $command = new ChangePasswordCommand($userManager, $userManipulator, $userProvider);

        $commandTester = $this->createCommandTester('makg:user:change-password', $command);
        $commandTester->setInputs(['']); // Leave password empty for random password
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Changed password for user makg@example.com', $output);
    }
}
