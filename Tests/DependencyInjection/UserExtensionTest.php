<?php

namespace MakG\UserBundle\Tests\DependencyInjection;


use MakG\UserBundle\DependencyInjection\UserExtension;
use MakG\UserBundle\UserBundle;
use PHPUnit\Framework\TestCase;

class UserExtensionTest extends TestCase
{
    public function testGetAlias()
    {
        $extension = new UserExtension();

        $this->assertEquals(UserBundle::EXTENSION_ALIAS, $extension->getAlias());
    }
}
