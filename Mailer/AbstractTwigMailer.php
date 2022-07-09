<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Dto\EmailMessage;
use MakG\UserBundle\Dto\EmailMessageContent;
use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

abstract class AbstractTwigMailer implements MailerInterface
{
    private $twig;
    private $router;
    private $senderEmail;
    private $senderName;

    public function __construct(Environment $twig, RouterInterface $router, ?string $sender)
    {
        $this->twig = $twig;
        $this->router = $router;

        $this->setSender($sender);
    }

    abstract function send(EmailMessage $emailMessage): void;

    public function sendConfirmationEmail(UserInterface $user): void
    {
        $messageContent = $this->createEmailMessageContentFromTemplate('@User/emails/confirmation.html.twig', [
            'user' => $user,
            'confirmationToken' => $user->getConfirmationToken(),
            'confirmationUrl' => $this->router->generate(
                'mg_user_registration_confirm',
                ['token' => $user->getConfirmationToken()],
                RouterInterface::ABSOLUTE_URL
            ),
        ]);
        $emailMessage = new EmailMessage($user->getEmail(), $this->senderEmail, $this->senderName, $messageContent);

        $this->send($emailMessage);
    }

    public function sendResettingEmail(UserInterface $user): void
    {
        $messageContent = $this->createEmailMessageContentFromTemplate('@User/emails/resetting.html.twig', [
            'user' => $user,
            'confirmationToken' => $user->getConfirmationToken(),
            'confirmationUrl' => $this->router->generate(
                'mg_user_resetting_reset',
                ['token' => $user->getConfirmationToken()],
                RouterInterface::ABSOLUTE_URL
            ),
        ]);
        $emailMessage = new EmailMessage($user->getEmail(), $this->senderEmail, $this->senderName, $messageContent);

        $this->send($emailMessage);
    }

    protected function createEmailMessageContentFromTemplate(string $template, array $context = []): EmailMessageContent
    {
        $template = $this->twig->load($template);

        $subject = $template->renderBlock('subject', $context);
        $bodyText = $template->renderBlock('body_text', $context);
        $bodyHtml = $template->renderBlock('body_html', $context);

        return new EmailMessageContent($subject, $bodyText, $bodyHtml);
    }

    private function setSender(?string $sender)
    {
        preg_match('/([^<]+)\s*(<.*>)?/', (string)$sender, $matches);

        $senderName = isset($matches[1]) ? trim($matches[1]) : null;
        $email = isset($matches[2]) ? trim($matches[2], " \t<>") : null;

        if (empty($email)) {
            $email = $matches[1] ?? null;
            $senderName = '';
        }

        $this->senderEmail = $email;
        $this->senderName = $senderName;
    }
}
