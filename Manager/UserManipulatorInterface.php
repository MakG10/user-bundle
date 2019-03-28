<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\Entity\UserInterface;

interface UserManipulatorInterface
{
    public function randomizeAvatar(UserInterface $user);
}