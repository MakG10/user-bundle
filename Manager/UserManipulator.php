<?php

namespace MakG\UserBundle\Manager;


use MakG\UserBundle\AvatarGenerator\AvatarGeneratorInterface;
use MakG\UserBundle\Entity\AvatarInterface;
use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserManipulator implements UserManipulatorInterface
{
    private $userManager;
    private $avatarGenerator;
    private $filesystem;
    private $tokenGenerator;

    public function __construct(
        UserManagerInterface $userManager,
        AvatarGeneratorInterface $avatarGenerator,
        Filesystem $filesystem,
        TokenGeneratorInterface $tokenGenerator
    )
    {
        $this->userManager = $userManager;
        $this->avatarGenerator = $avatarGenerator;
        $this->filesystem = $filesystem;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function randomizeAvatar(UserInterface $user): void
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

    public function generateRandomPassword(UserInterface $user): string
    {
        $randomPassword = $this->tokenGenerator->generateToken();

        $user->setPlainPassword($randomPassword);

        return $randomPassword;
    }

    public function generateRandomToken(UserInterface $user): string
    {
        $randomPassword = $this->tokenGenerator->generateToken();

        $user->setConfirmationToken($randomPassword);

        return $randomPassword;
    }

    public function addRole(UserInterface $user, string $role): void
    {
        if ($user->hasRole($role)) {
            return;
        }

        $user->addRole($role);
    }

    public function removeRole(UserInterface $user, string $role): void
    {
        if (!$user->hasRole($role)) {
            return;
        }

        $user->removeRole($role);
    }

    private function saveTemporaryFile($content): string
    {
        $filePath = $this->filesystem->tempnam(\sys_get_temp_dir(), 'avatar');

        $this->filesystem->dumpFile($filePath, $content);

        return $filePath;
    }
}
