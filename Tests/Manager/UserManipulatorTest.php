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
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserManipulatorTest extends TestCase
{

    public function testRandomizeAvatar()
    {
        $user = new TestUser();
        $user->setEmail('tester@example.org');

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

        $userManipulator = $this->getUserManipulator(
            [
                'avatarGenerator' => $avatarGenerator,
                'filesystem' => $filesystem,
            ]
        );

        $userManipulator->randomizeAvatar($user);

        $this->assertInstanceOf(UploadedFile::class, $user->getAvatarFile());
    }

    public function testRandomizeAvatarOnUserNotImplementingAvatarInterface()
    {
        $user = new User();

        $userManipulator = $this->getUserManipulator();

        $this->expectException(\InvalidArgumentException::class);
        $userManipulator->randomizeAvatar($user);
    }

    public function testGenerateRandomPassword()
    {
        $user = new TestUser();

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn('password');

        $userManipulator = $this->getUserManipulator(['tokenGenerator' => $tokenGenerator]);

        $userManipulator->generateRandomPassword($user);

        $this->assertSame('password', $user->getPlainPassword());
    }

    public function testGenerateRandomToken()
    {
        $user = new TestUser();

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator
            ->expects($this->once())
            ->method('generateToken')
            ->willReturn('token');

        $userManipulator = $this->getUserManipulator(['tokenGenerator' => $tokenGenerator]);

        $userManipulator->generateRandomToken($user);

        $this->assertSame('token', $user->getConfirmationToken());
    }

    public function testAddRole()
    {
        $user = new TestUser();

        $userManipulator = $this->getUserManipulator();

        $userManipulator->addRole($user, 'ROLE1');
        $userManipulator->addRole($user, 'ROLE2');

        $this->assertTrue($user->hasRole('ROLE1'));
        $this->assertTrue($user->hasRole('ROLE2'));
    }

    public function testRemoveRole()
    {
        $user = new TestUser();
        $user->setRoles(['ROLE1', 'ROLE2', 'ROLE3']);

        $userManipulator = $this->getUserManipulator();

        $userManipulator->removeRole($user, 'ROLE1');
        $userManipulator->removeRole($user, 'ROLE3');

        $this->assertFalse($user->hasRole('ROLE1'));
        $this->assertTrue($user->hasRole('ROLE2'));
        $this->assertFalse($user->hasRole('ROLE3'));
    }

    private function getUserManipulator(array $mocks = [])
    {
        $userManager = $mocks['userManager'] ?? $this->createMock(UserManagerInterface::class);
        $avatarGenerator = $mocks['avatarGenerator'] ?? $this->createMock(AvatarGeneratorInterface::class);
        $filesystem = $mocks['filesystem'] ?? $this->createMock(Filesystem::class);
        $tokenGenerator = $mocks['tokenGenerator'] ?? $this->createMock(TokenGeneratorInterface::class);

        return new UserManipulator($userManager, $avatarGenerator, $filesystem, $tokenGenerator);
    }
}
