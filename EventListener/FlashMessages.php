<?php

namespace MakG\UserBundle\EventListener;


use MakG\UserBundle\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashMessages implements EventSubscriberInterface
{
    private $session;
    private $translator;

    private static $messages = [
        UserEvent::REGISTRATION_CONFIRMED => 'registration.confirm.success',
        UserEvent::PASSWORD_RESET_COMPLETED => 'registration.confirm.success',
    ];

    public function __construct(Session $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvent::REGISTRATION_CONFIRMED => 'addSuccessMessage',
            UserEvent::PASSWORD_RESET_COMPLETED => 'addSuccessMessage',
        ];
    }

    public function addSuccessMessage(UserEvent $event, string $eventName): void
    {
        $message = $this->translator->trans(self::$messages[$eventName], [], 'MgUser');

        $this->session->getFlashBag()->add('success', $message);
    }
}
