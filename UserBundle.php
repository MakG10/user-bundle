<?php

namespace MakG\UserBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public const EXTENSION_ALIAS = 'makg_user';

    public function getContainerExtension()
    {
        return $this->createContainerExtension();
    }
}
