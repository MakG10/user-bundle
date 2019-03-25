<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Entity\UserInterface;

interface MailerInterface
{
    public function sendConfirmationEmail(UserInterface $user);
    public function sendResettingEmail(UserInterface $user);
}