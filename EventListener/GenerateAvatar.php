<?php


namespace MakG\UserBundle\EventListener;


use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\Manager\UserManipulatorInterface;

class GenerateAvatar
{
    private $userManipulator;

    public function __construct(UserManipulatorInterface $userManipulator)
    {
        $this->userManipulator = $userManipulator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::REGISTRATION_COMPLETED => 'generateAvatar',
        ];
    }

    public function generateAvatar(UserEvent $event)
    {
        $this->userManipulator->randomizeAvatar($event->getUser());
    }
}