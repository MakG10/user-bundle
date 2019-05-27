<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Entity\UserInterface;

interface MailerInterface
{
    public function sendConfirmationEmail(UserInterface $user): void;

    public function sendResettingEmail(UserInterface $user): void;
}