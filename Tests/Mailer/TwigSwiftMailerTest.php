<?php

namespace MakG\UserBundle\Tests\Mailer;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Mailer\TwigSwiftMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class TwigSwiftMailerTest extends TestCase
{
    public function testSendConfirmationEmail()
    {
        $mailer = $this->createMock(\Swift_Mailer::class);
        $mailer
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(
                    function (\Swift_Message $message) {
                        $from = $message->getFrom();

                        return array_keys($from)[0] === 'test@example.org';
                    }
                )
            );

        $twig = new \Twig\Environment(
            new \Twig\Loader\ArrayLoader(
                [
                    '@User/emails/confirmation.html.twig' => <<<TWIG
{% block subject %}subject{% endblock %}
{% block body_text %}text{% endblock %}
{% block body_html %}text{% endblock %}
TWIG
    ,
                ]
            )
        );

        $context = $this->createMock(RequestContext::class);

        $router = $this->createMock(RouterInterface::class);
        $router
            ->method('getContext')
            ->willReturn($context);

        $user = new User();
        $user->setEmail('user@example.org');

        $mailer = new TwigSwiftMailer($mailer, $twig, 'Name Surname <test@example.org>', $router);

        $mailer->sendConfirmationEmail($user);
    }
}
