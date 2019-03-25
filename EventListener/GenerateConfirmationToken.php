<?php

namespace MakG\UserBundle\EventListener;



use MakG\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class GenerateConfirmationToken implements EventSubscriberInterface
{
    private $tokenGenerator;

    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::REGISTRATION_SUCCESS => 'generateToken',
            UserEvent::PASSWORD_RESET_REQUESTED => 'generateToken',
        ];
    }

    public function generateToken(UserEvent $event): void
    {
        $user = $event->getUser();
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
    }
}
