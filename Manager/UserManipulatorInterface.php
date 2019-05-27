<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\Entity\UserInterface;

interface UserManipulatorInterface
{
    public function randomizeAvatar(UserInterface $user): void;

    public function generateRandomPassword(UserInterface $user): string;

    public function generateRandomToken(UserInterface $user): string;

    public function addRole(UserInterface $user, string $role): void;

    public function removeRole(UserInterface $user, string $role): void;
}