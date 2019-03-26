<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\Entity\UserInterface;

interface UserManagerInterface
{
    public function createUser(): UserInterface;
    public function updateUser(UserInterface $user): void;
    public function findUserBy(array $conditions): ?UserInterface;

    public function getUserClass(): ?string;
}