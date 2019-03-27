<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 27.03.19
 * Time: 15:06
 */

namespace MakG\UserBundle\Tests\EventListener;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\EventListener\GenerateConfirmationToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class GenerateConfirmationTokenTest extends TestCase
{

    public function testGenerateToken()
    {
        $user = new User();

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator
            ->method('generateToken')
            ->willReturn('token');

        $userEvent = new UserEvent($user);

        $listener = new GenerateConfirmationToken($tokenGenerator);
        $listener->generateToken($userEvent);

        $this->assertSame('token', $user->getConfirmationToken());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(GenerateConfirmationToken::getSubscribedEvents());
    }
}
