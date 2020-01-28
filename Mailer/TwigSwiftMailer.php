<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Dto\EmailMessage;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class TwigSwiftMailer extends AbstractTwigMailer
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, ?string $sender, RouterInterface $router)
    {
        parent::__construct($twig, $router, $sender);

        $this->mailer = $mailer;
    }

    public function send(EmailMessage $emailMessage): void
    {
        $messageContent = $emailMessage->messageContent;
        $message = (new \Swift_Message($messageContent->subject))
            ->setTo($emailMessage->recipientEmail)
            ->setFrom($emailMessage->senderEmail, $emailMessage->senderName)
            ->setBody($messageContent->bodyText, 'text/plain')
            ->addPart($messageContent->bodyHtml, 'text/html');

        $this->mailer->send($message);
    }
}
