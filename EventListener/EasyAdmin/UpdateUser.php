<?php

namespace MakG\UserBundle\EventListener\EasyAdmin;


use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class UpdateUser implements EventSubscriberInterface
{
    /** @var UserManagerInterface */
    private $userManager;

    /** @var UserManipulatorInterface */
    private $userManipulator;

    /** @var string */
    private $userClass;

    public function __construct(
        UserManagerInterface $userManager,
        UserManipulatorInterface $userManipulator,
        string $userClass
    ) {
        $this->userClass = $userClass;
        $this->userManipulator = $userManipulator;
        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            EasyAdminEvents::PRE_PERSIST => 'updateUser',
            EasyAdminEvents::PRE_UPDATE => 'updateUser',
        ];
    }

    public function updateUser(GenericEvent $event, string $eventName)
    {
        /** @var UserInterface $user */
        $user = $event->getSubject();

        if (!$user instanceof UserInterface || get_class($user) !== $this->userClass) {
            return;
        }

        if ($eventName === EasyAdminEvents::PRE_PERSIST && null === $user->getPlainPassword()) {
            $this->userManipulator->generateRandomPassword($user);
        }

        $this->userManager->updateUser($user);
    }
}
