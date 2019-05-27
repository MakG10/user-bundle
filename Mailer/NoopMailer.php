<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Entity\UserInterface;

class NoopMailer implements MailerInterface
{
    public function sendConfirmationEmail(UserInterface $user): void
    {
    }

    public function sendResettingEmail(UserInterface $user): void
    {
    }
}
