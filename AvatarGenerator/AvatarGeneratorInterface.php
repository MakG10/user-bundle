<?php

namespace MakG\UserBundle\AvatarGenerator;


interface AvatarGeneratorInterface
{
    public function generate(?string $salt, array $options = []);
}
