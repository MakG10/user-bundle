<?php

namespace MakG\UserBundle\Tests\Form;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Entity\UserInterface;
use MakG\UserBundle\Form\RegistrationForm;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class RegistrationFormTest extends FormIntegrationTestCase
{
    public function testSubmit()
    {
        $form = $this->factory->create(RegistrationForm::class);

        $data = [
            'email' => 'tester@example.org',
            'displayName' => 'user',
            'plainPassword' => [
                'first' => 'password',
                'second' => 'password',
            ],
        ];

        $form->submit($data);

        /** @var User $user */
        $user = $form->getData();

        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertSame('tester@example.org', $user->getEmail());
        $this->assertSame('user', $user->getDisplayName());
        $this->assertSame('password', $user->getPlainPassword());
    }
}
