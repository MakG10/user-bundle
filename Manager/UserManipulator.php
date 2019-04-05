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
    private $filesystem;

    public function __construct(
        UserManagerInterface $userManager,
        AvatarGeneratorInterface $avatarGenerator,
        Filesystem $filesystem
    )
    {
        $this->userManager = $userManager;
        $this->avatarGenerator = $avatarGenerator;
        $this->filesystem = $filesystem;
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
        $filePath = $this->filesystem->tempnam(\sys_get_temp_dir(), 'avatar');

        $this->filesystem->dumpFile($filePath, $content);

        return $filePath;
    }
}
