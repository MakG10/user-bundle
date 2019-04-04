<?php

namespace MakG\UserBundle\Tests\EventListener;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\EventListener\AuthenticateUser;
use MakG\UserBundle\Security\LoginManagerInterface;
use PHPUnit\Framework\TestCase;

class AuthenticateUserTest extends TestCase
{

    public function testAuthenticateUser()
    {
        $user = new User();

        $loginManager = $this->createMock(LoginManagerInterface::class);
        $loginManager
            ->expects($this->once())
            ->method('authenticateUser')
            ->with($user);

        $userEvent = new UserEvent($user);

        $listener = new AuthenticateUser($loginManager);
        $listener->authenticateUser($userEvent);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(AuthenticateUser::getSubscribedEvents());
    }
}
