<?php

namespace MakG\UserBundle\EventListener;



use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendEmails implements EventSubscriberInterface
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::REGISTRATION_COMPLETED => 'sendConfirmationEmail',
            UserEvent::PASSWORD_RESET_REQUESTED => 'sendResettingEmail',
        ];
    }

    /**
     * Sends confirmation e-mail after registration
     */
    public function sendConfirmationEmail(UserEvent $event): void
    {
        $user = $event->getUser();

        // Skip confirmation e-mail for already confirmed user
        if ($user->getEnabled()) {
            return;
        }

        if (null === $user->getConfirmationToken()) {
            $this->logger->error(
                'User is not enabled and confirmation token is empty - impossible to send a confirmation e-mail.',
                ['user' => $user->getId()]
            );

            return;
        }


        $this->mailer->sendConfirmationEmail($user);
    }

    public function sendResettingEmail(UserEvent $event): void
    {
        $user = $event->getUser();

        if (!$user->isEnabled()) {
            $this->logger->error(
                'User is not enabled - refusing to send a resetting e-mail.',
                ['user' => $user->getId()]
            );

            return;
        }

        if (null === $user->getConfirmationToken()) {
            $this->logger->error(
                'Confirmation token is empty - refusing to send a resetting e-mail.',
                ['user' => $user->getId()]
            );

            return;
        }


        $this->mailer->sendResettingEmail($user);
    }
}
