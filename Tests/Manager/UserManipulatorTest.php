<?php

namespace MakG\UserBundle\Tests\Manager;

use MakG\UserBundle\AvatarGenerator\AvatarGeneratorInterface;
use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Manager\UserManipulator;
use MakG\UserBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserManipulatorTest extends TestCase
{

    public function testRandomizeAvatar()
    {
        $user = new TestUser();
        $user->setEmail('tester@example.org');

        $userManager = $this->createMock(UserManagerInterface::class);

        $avatarGenerator = $this->createMock(AvatarGeneratorInterface::class);
        $avatarGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($user->getEmail())
            ->willReturn('blob');

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->method('tempnam')
            ->willReturn(__FILE__);
        $filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with(__FILE__, 'blob');

        $userManipulator = new UserManipulator($userManager, $avatarGenerator, $filesystem);

        $userManipulator->randomizeAvatar($user);

        $this->assertInstanceOf(UploadedFile::class, $user->getAvatarFile());
    }

    public function testRandomizeAvatarOnUserNotImplementingAvatarInterface()
    {
        $user = new User();

        $userManager = $this->createMock(UserManagerInterface::class);
        $avatarGenerator = $this->createMock(AvatarGeneratorInterface::class);
        $filesystem = $this->createMock(Filesystem::class);

        $userManipulator = new UserManipulator($userManager, $avatarGenerator, $filesystem);

        $this->expectException(\InvalidArgumentException::class);
        $userManipulator->randomizeAvatar($user);
    }
}
