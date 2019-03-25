<?php

namespace MakG\UserBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="email", message="Email already taken")
 * @Vich\Uploadable()
 */
class User implements UserInterface
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $displayName;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $password;

	/**
	 * @ORM\Column(type="array")
	 */
	private $roles = [];

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $enabled = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordRequestedAt;

	/**
	 * Virtual property
	 */
	private $plainPassword;

	public function __construct()
    {
    }

	public function getId(): ?int
    {
        return $this->id;
    }

	public function getEmail(): ?string
    {
        return $this->email;
    }

	public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

	public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

	public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

	public function getPassword(): ?string
    {
        return $this->password;
    }

	public function setPassword(string $password): self
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

	public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): self
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
        // TODO: Implement isEnabled() method.
    }

    public function hasPasswordRequestExpired(int $ttl): bool
    {
        // TODO: Implement hasPasswordRequestExpired() method.
    }
}
