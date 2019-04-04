<?php

namespace MakG\UserBundle\Tests\Form;

use MakG\UserBundle\Entity\User;
use MakG\UserBundle\Form\ResetPasswordForm;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class ResetPasswordFormTest extends FormIntegrationTestCase
{
    public function testSubmit()
    {
        $user = new User();

        $form = $this->factory->create(ResetPasswordForm::class, $user);

        $data = [
            'plainPassword' => [
                'first' => 'password',
                'second' => 'password',
            ],
        ];

        $form->submit($data);

        $this->assertSame($user, $form->getData());
        $this->assertSame('password', $user->getPlainPassword());
    }
}
