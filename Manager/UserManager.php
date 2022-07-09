<?php

namespace MakG\UserBundle\Manager;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use MakG\UserBundle\Entity\AvatarInterface;
use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager implements UserManagerInterface
{
    private $userClass;
    private $entityManager;
    private $passwordEncoder;

    public function __construct(
        string                      $userClass,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordEncoder
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

        // Workaround for VichUploader to persist avatar even if there are no other changes to persist in the entity
        if ($user instanceof AvatarInterface && null !== $user->getAvatarFile() && empty($this->getChangeSet($user))) {
            $event = new LifecycleEventArgs($user, $this->entityManager);
            $this->entityManager->getEventManager()->dispatchEvent(Events::preUpdate, $event);
        }

        $this->entityManager->persist($user);
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
        $encodedPassword = $this->passwordEncoder->hashPassword($user, $user->getPlainPassword());

        $user->setPassword($encodedPassword);
    }

    private function getChangeSet(UserInterface $user): array
    {
        return $this->entityManager->getUnitOfWork()->getEntityChangeSet($user);
    }
}
