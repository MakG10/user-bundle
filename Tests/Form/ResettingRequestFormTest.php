<?php

namespace MakG\UserBundle\Tests\Form;

use MakG\UserBundle\Form\ResettingRequestForm;
use Symfony\Component\Form\Test\FormIntegrationTestCase;

class ResettingRequestFormTest extends FormIntegrationTestCase
{
    public function testSubmit()
    {
        $form = $this->factory->create(ResettingRequestForm::class);

        $data = [
            'email' => 'tester@example.org',
        ];

        $form->submit($data);

        $this->assertSame($data, $form->getData());
    }
}
