<?php

namespace MakG\UserBundle\Security;


use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userManager->findUserBy(['email' => $identifier]);

        if (null === $user) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user): \Symfony\Component\Security\Core\User\UserInterface
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(
                sprintf('Expected an instance of %s, but got %s', UserInterface::class, get_class($user))
            );
        }


        $refreshedUser = $this->userManager->findUserBy(['id' => $user->getId()]);

        if (null === $refreshedUser) {
            throw new UserNotFoundException(
                sprintf('User with ID "%s" was not found during refresh.', $user->getId())
            );
        }

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class): bool
    {
        $userClass = $this->userManager->getUserClass();

        return $class === $userClass || is_subclass_of($class, $userClass);
    }
}
