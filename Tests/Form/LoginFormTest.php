<?php
/**
 * Created by PhpStorm.
 * User: maciej
 * Date: 27.03.19
 * Time: 15:43
 */

namespace MakG\UserBundle\Tests\Form;

use MakG\UserBundle\Form\LoginForm;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class LoginFormTest extends FormIntegrationTestCase
{
    public function testSubmit()
    {
        $form = $this->factory->create(LoginForm::class);

        $data = [
            '_username' => 'user',
            '_password' => 'password',
        ];

        $form->submit($data);

        $this->assertSame($data, $form->getData());
    }
}
