<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 27.03.19
 * Time: 14:26
 */

namespace MakG\UserBundle\Tests\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Manager\UserManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManagerTest extends TestCase
{
    /** @var UserManager */
    private $userManager;

    /** @var MockObject */
    private $entityManager;

    /** @var MockObject */
    private $passwordEncoder;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->userManager = new UserManager(User::class, $this->entityManager, $this->passwordEncoder);
    }

    public function testFindUserBy()
    {
        $user = new User();

        $repository = $this->createMock(EntityRepository::class);
        $repository
            ->method('findOneBy')
            ->with(['id' => 123])
            ->willReturn($user);

        $this->entityManager
            ->method('getRepository')
            ->with($this->userManager->getUserClass())
            ->willReturn($repository);

        $this->assertSame($user, $this->userManager->findUserBy(['id' => 123]));
    }

    public function testUpdateUser()
    {
        $user = new User();
        $user->setPlainPassword('password');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->passwordEncoder
            ->method('encodePassword')
            ->with($user, $user->getPlainPassword())
            ->willReturn('encoded password');

        $this->userManager->updateUser($user);

        $this->assertSame('encoded password', $user->getPassword());
    }

    public function testCreateUser()
    {
        $user = $this->userManager->createUser();

        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetUserClass()
    {
        $this->assertSame(User::class, $this->userManager->getUserClass());
    }
}
