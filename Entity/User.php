<?php

namespace MakG\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\MappedSuperclass
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface, EquatableInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
    protected $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
    protected $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $displayName;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
    protected $password;

	/**
	 * @ORM\Column(type="array")
	 */
    protected $roles = [];

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $enabled = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

	/**
	 * Virtual property
	 */
    protected $plainPassword;

	public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

	public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = mb_strtolower($email);

        return $this;
    }

	public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

	public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

	public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;

        return $this;
    }

	public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

	public function setPlainPassword(?string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

	public function getSalt()
    {
        return null;
    }

	public function getUsername(): ?string
    {
        return $this->getEmail();
    }

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

	public function __toString()
	{
		return (string) $this->getDisplayName();
	}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function hasPasswordRequestExpired(int $ttl): bool
    {
        if (null === $this->getPasswordRequestedAt()) {
            return true;
        }

        $now = new \DateTime();

        return ($now->getTimestamp() - $this->getPasswordRequestedAt()->getTimestamp()) > $ttl;
    }

    /**
     * User should be required to re-authenticate when his username/email changes or if his account gets disabled
     * during active sesion.
     */
    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user): bool
    {
        return $user->getUsername() === $this->getUsername() && $this->isEnabled();
    }
}
