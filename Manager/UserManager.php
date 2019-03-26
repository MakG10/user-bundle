<?php

namespace MakG\UserBundle\Manager;


use Doctrine\ORM\EntityManagerInterface;
use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager implements UserManagerInterface
{
    private $userClass;
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        string $userClass,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->userClass = $userClass;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(): UserInterface
    {
        return new $this->userClass;
    }

    public function updateUser(UserInterface $user): void
    {
        if (!empty($user->getPlainPassword())) {
            $this->updatePassword($user);
        }

        $this->entityManager->flush();
    }

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->entityManager->getRepository($this->userClass)->findOneBy($criteria);
    }

    public function getUserClass(): ?string
    {
        return $this->userClass;
    }


    private function updatePassword(UserInterface $user)
    {
        $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
    }
}