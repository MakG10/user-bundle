<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 27.03.19
 * Time: 13:46
 */

namespace MakG\UserBundle\Tests\Security;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Manager\UserManagerInterface;
use MakG\UserBundle\Security\UserProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProviderTest extends TestCase
{
    /** @var UserProvider */
    private $userProvider;

    /** @var MockObject */
    private $userManager;

    protected function setUp(): void
    {
        $this->userManager = $this->createMock(UserManagerInterface::class);
        $this->userProvider = new UserProvider($this->userManager);
    }

    public function testLoadUserByUsername()
    {
        $user = new User();

        $this->userManager
            ->method('findUserBy')
            ->with(['email' => 'user@example.org'])
            ->willReturn($user);

        $this->assertSame($user, $this->userProvider->loadUserByUsername('user@example.org'));
    }

    public function testLoadNonExistingUser()
    {
        $this->userManager
            ->method('findUserBy')
            ->with(['email' => 'user@example.org'])
            ->willReturn(null);

        $this->expectException(UsernameNotFoundException::class);
        $this->userProvider->loadUserByUsername('user@example.org');
    }

    public function testRefreshUser()
    {
        $user = new User();

        $this->userManager
            ->method('findUserBy')
            ->with(['id' => null])
            ->willReturn($user);

        $this->assertSame($user, $this->userProvider->refreshUser($user));
    }

    public function testRefreshUnsupportedUser()
    {
        $user = $this->createMock(UserInterface::class);

        $this->expectException(UnsupportedUserException::class);
        $this->assertSame($user, $this->userProvider->refreshUser($user));
    }

    public function testRefreshDeletedUser()
    {
        $user = new User();

        $this->userManager
            ->method('findUserBy')
            ->with(['id' => null])
            ->willReturn(null);

        $this->expectException(UsernameNotFoundException::class);
        $this->userProvider->refreshUser($user);
    }

    public function testSupportsClass()
    {
        $this->userManager
            ->expects($this->once())
            ->method('getUserClass')
            ->willReturn(User::class);

        $this->assertTrue($this->userProvider->supportsClass(User::class));
    }
}
