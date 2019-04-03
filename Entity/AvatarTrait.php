<?php

namespace MakG\UserBundle\Entity;


use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait AvatarTrait
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     *
     * @Vich\UploadableField(mapping="user_avatars", fileNameProperty="avatar")
     * @Assert\Image(maxSize="100k", mimeTypes={"image/jpeg", "image/jpg", "image/png", "image/gif"})
     */
    private $avatarFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    public function setAvatarFile($avatarFile)
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }
}