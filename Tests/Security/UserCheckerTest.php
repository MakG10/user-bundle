<?php

namespace MakG\UserBundle\Tests\Security;

use MakG\UserBundle\Security\UserChecker;
use MakG\UserBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;

class UserCheckerTest extends TestCase
{

    public function testDisabledUser()
    {
        $this->expectException(DisabledException::class);

        $user = new TestUser();
        $user->setEnabled(false);

        $userChecker = new UserChecker();
        $userChecker->checkPreAuth($user);
        $userChecker->checkPostAuth($user);
    }

    public function testEnabledUser()
    {
        // No exceptions are expected
        $this->expectNotToPerformAssertions();

        $user = new TestUser();
        $user->setEnabled(true);

        $userChecker = new UserChecker();
        $userChecker->checkPreAuth($user);
        $userChecker->checkPostAuth($user);
    }

    public function testUnsupportedUserClass()
    {
        // No exceptions are expected
        $this->expectNotToPerformAssertions();

        $user = new \Symfony\Component\Security\Core\User\User('user', 'pass');

        $userChecker = new UserChecker();
        $userChecker->checkPreAuth($user);
        $userChecker->checkPostAuth($user);
    }
}
