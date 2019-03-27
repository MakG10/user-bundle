<?php

namespace MakG\UserBundle\Security;


use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginManager implements LoginManagerInterface
{
    private $tokenStorage;
    private $firewallName;

    public function __construct(TokenStorageInterface $tokenStorage, $firewallName)
    {
        $this->tokenStorage = $tokenStorage;
        $this->firewallName = $firewallName;
    }

    public function authenticateUser(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, null, $this->firewallName, $user->getRoles());

        $this->tokenStorage->setToken($token);
    }
}
