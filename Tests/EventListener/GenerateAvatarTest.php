<?php

namespace MakG\UserBundle\Tests\EventListener;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\EventListener\GenerateAvatar;
use MakG\UserBundle\Manager\UserManipulatorInterface;
use PHPUnit\Framework\TestCase;

class GenerateAvatarTest extends TestCase
{

    public function testRandomizeAvatar()
    {
        $user = new User();

        $userManipulator = $this->createMock(UserManipulatorInterface::class);
        $userManipulator
            ->expects($this->once())
            ->method('randomizeAvatar')
            ->with($user);

        $userEvent = new UserEvent($user);

        $listener = new GenerateAvatar($userManipulator);
        $listener->generateAvatar($userEvent);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(GenerateAvatar::getSubscribedEvents());
    }
}
