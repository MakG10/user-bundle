<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 27.03.19
 * Time: 14:26
 */

namespace MakG\UserBundle\Tests\Manager;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Manager\UserManager;
use MakG\UserBundle\Tests\TestUser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
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

    public function testUpdateUserAvatar()
    {
        $file = new File(__FILE__);

        $user = new TestUser();
        $user->setAvatarFile($file);

        $eventManager = $this->createMock(EventManager::class);
        $eventManager
            ->expects($this->once())
            ->method('dispatchEvent');

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork
            ->method('getEntityChangeSet')
            ->willReturn([]);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->entityManager
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $this->entityManager
            ->method('getEventManager')
            ->willReturn($eventManager);

        $this->userManager->updateUser($user);
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
