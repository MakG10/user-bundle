<?php

namespace MakG\UserBundle\Security;


use MakG\UserBundle\Entity\UserInterface;

interface LoginManagerInterface
{
    /**
     * Authenticates given user.
     */
    public function authenticateUser(UserInterface $user): void;
}
