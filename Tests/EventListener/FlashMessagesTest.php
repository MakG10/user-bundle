<?php

namespace MakG\UserBundle\Tests\EventListener;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\EventListener\AuthenticateUser;
use MakG\UserBundle\EventListener\FlashMessages;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashMessagesTest extends TestCase
{

    public function testAddSuccessMessage()
    {
        $eventName = UserEvent::REGISTRATION_CONFIRMED;
        $user = new User();

        $session = $this->createMock(Session::class);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects($this->once())
            ->method('trans')
            ->willReturn('message');

        $userEvent = new UserEvent($user);

        $listener = new FlashMessages($session, $translator);
        $listener->addSuccessMessage($userEvent, $eventName);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(AuthenticateUser::getSubscribedEvents());
    }
}
