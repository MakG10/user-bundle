<?php

namespace MakG\UserBundle\Tests\Command;


use MakG\UserBundle\Command\CreateUserCommand;
use MakG\UserBundle\Mailer\MailerInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use MakG\UserBundle\Tests\CommandTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommandTest extends CommandTestCase
{
    public function testExecute()
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager
            ->expects($this->once())
            ->method('createUser');

        $userManipulator = $this->createMock(UserManipulatorInterface::class);

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('sendConfirmationEmail');
        $mailer
            ->expects($this->once())
            ->method('sendResettingEmail');

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([]));

        $command = new CreateUserCommand($userManager, $userManipulator, $mailer, $validator);

        $commandTester = $this->createCommandTester('makg:user:create', $command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'email' => 'makg@example.com',
                '--send-confirmation-email' => true,
                '--send-resetting-email' => true,
            ]
        );

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Created user', $output);
        $this->assertStringContainsString('Sent confirmation e-mail', $output);
        $this->assertStringContainsString('Sent password resetting e-mail', $output);
    }
}
