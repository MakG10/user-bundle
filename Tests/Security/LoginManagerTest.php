<?php

namespace MakG\UserBundle\Tests\Security;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Security\LoginManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginManagerTest extends TestCase
{
    public function testAuthenticateUser()
    {
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($this->isInstanceOf(TokenInterface::class));

        $loginManager = new LoginManager($tokenStorage, 'main');
        $user = new User();

        $loginManager->authenticateUser($user);
    }
}
