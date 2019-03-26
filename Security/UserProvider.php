<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 26.03.19
 * Time: 15:41
 */

namespace MakG\UserBundle\Security;


use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserBy(['email' => $username]);

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(
                sprintf('Expected an instance of %s, but got %s', UserInterface::class, get_class($user))
            );
        }


        $refreshedUser = $this->userManager->findUserBy(['id' => $user->getId()]);

        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(
                sprintf('User with ID "%s" was not found during refresh.', $user->getId())
            );
        }

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $userClass = $this->userManager->getUserClass();

        return $class === $userClass || is_subclass_of($class, $userClass);
    }
}