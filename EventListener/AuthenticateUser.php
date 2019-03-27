<?php

namespace MakG\UserBundle\EventListener;


use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthenticateUser implements EventSubscriberInterface
{
    private $loginManager;

    public function __construct(LoginManagerInterface $loginManager)
    {
        $this->loginManager = $loginManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::REGISTRATION_CONFIRMED => 'authenticateUser',
            UserEvent::PASSWORD_RESET_COMPLETED => 'authenticateUser',
        ];
    }

    public function authenticateUser(UserEvent $event): void
    {
        $user = $event->getUser();

        $this->loginManager->authenticateUser($user);
    }
}
