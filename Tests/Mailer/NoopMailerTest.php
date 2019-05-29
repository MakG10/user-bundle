<?php

namespace MakG\UserBundle\Tests\Mailer;

use MakG\UserBundle\Mailer\NoopMailer;
use MakG\UserBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;

class NoopMailerTest extends TestCase
{

    public function testSendConfirmationEmail()
    {
        // NoopMailer does nothing...
        $this->expectNotToPerformAssertions();

        $user = new TestUser();

        $mailer = new NoopMailer();
        $mailer->sendConfirmationEmail($user);
    }

    public function testSendResettingEmail()
    {
        // NoopMailer does nothing...
        $this->expectNotToPerformAssertions();

        $user = new TestUser();

        $mailer = new NoopMailer();
        $mailer->sendResettingEmail($user);
    }
}
