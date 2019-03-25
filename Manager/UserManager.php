<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Entity\UserInterface;

class UserManager implements UserManagerInterface
{

    public function createUser(): UserInterface
    {
        return new User();
    }

    public function updateUser(UserInterface $user): void
    {
        // TODO: Implement updateUser() method.
    }

    public function findUserBy(array $conditions): ?UserInterface
    {
        // TODO: Implement findUserBy() method.
    }
}