<?php

namespace MakG\UserBundle\Tests\EventListener;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\EventListener\SendEmails;
use MakG\UserBundle\Mailer\MailerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SendEmailsTest extends TestCase
{
    public function testSendConfirmationEmail()
    {
        $user = new User();
        $user->setEnabled(false);
        $user->setConfirmationToken('token');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('sendConfirmationEmail')
            ->with($user);

        $logger = $this->createMock(LoggerInterface::class);

        $userEvent = new UserEvent($user);

        $listener = new SendEmails($mailer, $logger);
        $listener->sendConfirmationEmail($userEvent);
    }

    public function testSendResettingEmail()
    {
        $user = new User();
        $user->setEnabled(true);
        $user->setConfirmationToken('token');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($this->once())
            ->method('sendResettingEmail')
            ->with($user);

        $logger = $this->createMock(LoggerInterface::class);

        $userEvent = new UserEvent($user);

        $listener = new SendEmails($mailer, $logger);
        $listener->sendResettingEmail($userEvent);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(SendEmails::getSubscribedEvents());
    }
}
