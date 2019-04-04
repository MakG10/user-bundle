<?php


namespace MakG\UserBundle\Tests;


use MakG\UserBundle\Entity\AvatarInterface;
use MakG\UserBundle\Entity\AvatarTrait;
use MakG\UserBundle\Entity\User;

class TestUser extends User implements AvatarInterface
{
    use AvatarTrait;
}
