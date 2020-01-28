<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Dto\EmailMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class TwigSymfonyMailer extends AbstractTwigMailer
{
    private $mailer;

    public function __construct(
        \Symfony\Component\Mailer\MailerInterface $mailer,
        Environment $twig,
        RouterInterface $router,
        ?string $sender
    )
    {
        parent::__construct($twig, $router, $sender);

        $this->mailer = $mailer;
    }

    public function send(EmailMessage $emailMessage): void
    {
        $messageContent = $emailMessage->messageContent;
        $message = (new Email())
            ->from(new Address($emailMessage->senderEmail, $emailMessage->senderName))
            ->to($emailMessage->recipientEmail)
            ->subject($messageContent->subject)
            ->text($messageContent->bodyText)
            ->html($messageContent->bodyHtml);

        $this->mailer->send($message);
    }
}
