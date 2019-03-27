<?php

namespace MakG\UserBundle\Security;


use MakG\UserBundle\Entity\UserInterface;

interface LoginManagerInterface
{
    public function authenticateUser(UserInterface $user);
}
