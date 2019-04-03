<?php

namespace MakG\UserBundle\Entity;


interface AvatarInterface
{
    public function getAvatarFile();
    public function setAvatarFile($file);

    public function setAvatar(?string $avatar);
}