<?php

namespace MakG\UserBundle\Mailer;


use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class TwigSwiftMailer implements MailerInterface
{
    private $mailer;
    private $twig;
    private $sender;
    private $router;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig,
        ?string $sender,
        RouterInterface $router
    )
    {
        $this->mailer = $mailer;
        $this->twig   = $twig;
        $this->sender = $sender;
        $this->router = $router;
    }

    public function send(\Swift_Message $message)
    {
        return $this->mailer->send($message);
    }

    public function sendConfirmationEmail(UserInterface $user)
    {
        $this->sendEmail([
            'recipient'       => $user->getEmail(),
            'template'        => '@User/emails/confirmation.html.twig',
            'template_params' => [
                'user'              => $user,
                'confirmationToken' => $user->getConfirmationToken(),
                'confirmationUrl'   => $this->router->generate(
                    'mg_user_registration_confirm',
                    ['token' => $user->getConfirmationToken()]
                ),
            ],
        ]);
    }

    public function sendResettingEmail(UserInterface $user)
    {
        $this->sendEmail([
            'recipient'       => $user->getEmail(),
            'template'        => '@User/emails/resetting.html.twig',
            'template_params' => [
                'user'              => $user,
                'confirmationToken' => $user->getConfirmationToken(),
                'confirmationUrl'   => $this->router->generate(
                    'mg_user_resetting_reset',
                    ['token' => $user->getConfirmationToken()]
                ),
            ],
        ]);
    }

    protected function sendEmail(array $options = []): void
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
        $senderData = $this->getSenderData();

        // Override subject and body from provided template
        if ($options['template']) {
            $template = $this->twig->load($options['template']);

            $subject = $template->renderBlock('subject', $options['template_params']);
            $bodyText = $template->renderBlock('body_text', $options['template_params']);
            $bodyHtml = $template->renderBlock('body_html', $options['template_params']);
        }

        $message = (new \Swift_Message($subject))
            ->setTo($recipient)
            ->setFrom($senderData[0], $senderData[1])
            ->setBody($bodyText, 'text/plain')
            ->addPart($bodyHtml, 'text/html');

        $this->mailer->send($message);
    }

    private function getSenderData()
    {
        preg_match('/([^<]+)\s*(<.*>)?/', $this->sender, $matches);

        $senderName = isset($matches[1]) ? trim($matches[1]) : null;
        $email = isset($matches[2]) ? trim($matches[2], " \t<>") : null;

        if (empty($email)) {
            $email      = $matches[1];
            $senderName = null;
        }

        return [$email, $senderName];
    }
}