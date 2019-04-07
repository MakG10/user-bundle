<?php

namespace MakG\UserBundle\Security;


use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function checkPreAuth(UserInterface $user)
    {
        if ( ! $user instanceof \MakG\UserBundle\Entity\UserInterface) {
            return;
        }

        if ( ! $user->isEnabled()) {
            $exception = new DisabledException();
            $exception->setUser($user);

            throw $exception;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function checkPostAuth(UserInterface $user)
    {
        if ( ! $user instanceof \MakG\UserBundle\Entity\UserInterface) {
            return;
        }

        // TODO?
    }
}