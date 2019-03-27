<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwigSwiftMailer implements MailerInterface
{
    private $mailer;
    private $twig;
    private $sender;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, ?string $sender)
    {
        $this->mailer = $mailer;
        $this->twig   = $twig;
        $this->sender = $sender;
    }

    public function send(\Swift_Message $message)
    {
        return $this->mailer->send($message);
    }

    public function sendConfirmationEmail(UserInterface $user)
    {
        $this->sendEmail([
            'recipient' => $user->getEmail(),
            'template' => '@User/emails/resetting.html.twig',
            'template_params' => [
                'user' => $user,
                'confirmationToken' => $user->getConfirmationToken(),
            ],
        ]);
    }

    public function sendResettingEmail(UserInterface $user)
    {
        $this->sendEmail([
            'recipient' => $user->getEmail(),
            'template' => '@User/emails/confirmation.html.twig',
            'template_params' => [
                'user' => $user,
                'confirmationToken' => $user->getConfirmationToken(),
            ],
        ]);
    }

    protected function sendEmail(array $options = []): \Swift_Message
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'template' => null,
                'template_params' => [],
                'subject' => null,
                'content' => null,
            ]
        );
        $resolver->setRequired(['recipient']);

        $options = $resolver->resolve($options);


        $subject = $options['subject'];
        $bodyText = $options['content'];
        $bodyHtml = $options['content'];
        $recipient = $options['recipient'];

        // Override subject and body from provided template
        if ($options['template']) {
            $template = $this->twig->load($options['template']);

            $subject = $template->renderBlock('subject', $options['template_params']);
            $bodyText = $template->renderBlock('body_text', $options['template_params']);
            $bodyHtml = $template->renderBlock('body_html', $options['template_params']);
        }

        $message = (new \Swift_Message($subject))
            ->setTo($recipient)
            ->setBody($bodyText, 'text/plain')
            ->addPart($bodyHtml, 'text/html');

        return $message;
    }

    private function getSenderData()
    {
        preg_match('/(.*)(<.*>)?/', $this->sender, $matches);

        $senderName = $matches[1];
        $email      = trim($matches[2], " \t<>");

        if (empty($email)) {
            $email      = $matches[1];
            $senderName = null;
        }

        return [$email, $senderName];
    }
}