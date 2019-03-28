<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\AvatarGenerator\AvatarGeneratorInterface;
use MakG\UserBundle\Entity\AvatarInterface;
use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserManipulator implements UserManipulatorInterface
{
    private $userManager;
    private $avatarGenerator;

    public function __construct(UserManagerInterface $userManager, AvatarGeneratorInterface $avatarGenerator)
    {
        $this->userManager = $userManager;
        $this->avatarGenerator = $avatarGenerator;
    }

    public function randomizeAvatar(UserInterface $user)
    {
        if (!$user instanceof AvatarInterface) {
            throw new \InvalidArgumentException(sprintf('User must implement %s interface', AvatarInterface::class));
        }

        $avatarData = $this->avatarGenerator->generate($user->getEmail());
        $temporaryFilePath = $this->saveTemporaryFile($avatarData);
        $uploadedFile = new UploadedFile($temporaryFilePath, \basename($temporaryFilePath), null, null, true);

        $user->setAvatarFile($uploadedFile);

        $this->userManager->updateUser($user);
    }

    private function saveTemporaryFile($content): string
    {
        $filesystem = new Filesystem();
        $filePath = $filesystem->tempnam(\sys_get_temp_dir(), 'avatar');

        \file_put_contents($filePath, $content);

        return $filePath;
    }
}
