<?php

namespace MakG\UserBundle\Tests\Mailer;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Mailer\TwigSwiftMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class TwigSwiftMailerTest extends TestCase
{
    public function testSendConfirmationEmail()
    {
        $params = [
            'sender_email' => 'test@example.com',
            'sender_name' => 'Name Surname',
            'email' => 'user@example.com',
            'subject' => 'subject',
            'body_text' => 'text',
            'body_html' => 'html body',
        ];

        $mailer = $this->getSwiftMailerMock($params);
        $twig = $this->getTwig('@User/emails/confirmation.html.twig', $params);
        $router = $this->createMock(RouterInterface::class);

        $user = new User();
        $user->setEmail($params['email']);

        $mailer = new TwigSwiftMailer($mailer, $twig, "{$params['sender_name']} <{$params['sender_email']}>", $router);

        $mailer->sendConfirmationEmail($user);
    }

    public function testSendResettingEmail()
    {
        $params = [
            'sender_email' => 'test@example.com',
            'sender_name' => null,
            'email' => 'user2@example.com',
            'subject' => 'subject2',
            'body_text' => 'text2',
            'body_html' => 'html body2',
        ];

        $mailer = $this->getSwiftMailerMock($params);
        $twig = $this->getTwig('@User/emails/resetting.html.twig', $params);
        $router = $this->createMock(RouterInterface::class);

        $user = new User();
        $user->setEmail($params['email']);

        $mailer = new TwigSwiftMailer($mailer, $twig, $params['sender_email'], $router);

        $mailer->sendResettingEmail($user);
    }

    private function getSwiftMailerMock(array $assertions)
    {
        $mailer = $this->createMock(\Swift_Mailer::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(
                    function (\Swift_Message $message) use ($assertions) {
                        $from = $message->getFrom();
                        $to = $message->getTo();
                        $subject = $message->getSubject();
                        $bodyText = $message->getBody();
                        $bodyHtml = $message->getChildren()[0]->getBody();

                        $this->assertSame($assertions['sender_email'], array_keys($from)[0]);
                        $this->assertSame($assertions['sender_name'], array_values($from)[0]);
                        $this->assertSame($assertions['email'], array_keys($to)[0]);
                        $this->assertSame($assertions['subject'], $subject);
                        $this->assertSame($assertions['body_text'], $bodyText);
                        $this->assertSame($assertions['body_html'], $bodyHtml);

                        return true;
                    }
                )
            );

        return $mailer;
    }

    private function getTwig(string $template, array $params)
    {
        $twig = new \Twig\Environment(
            new \Twig\Loader\ArrayLoader(
                [
                    $template => <<<TWIG
{% block subject %}{$params['subject']}{% endblock %}
{% block body_text %}{$params['body_text']}{% endblock %}
{% block body_html %}{$params['body_html']}{% endblock %}
TWIG
                    ,
                ]
            )
        );

        return $twig;
    }
}
