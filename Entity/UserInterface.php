<?php

namespace MakG\UserBundle\Entity;


interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getId();
    public function getEmail(): ?string;

    public function setEmail(?string $email);
    public function isEnabled(): bool;
    public function setEnabled(bool $enabled);
    public function getConfirmationToken(): ?string;
    public function setConfirmationToken(?string $token);

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt);
    public function hasPasswordRequestExpired(int $ttl): bool;
    public function setPassword(string $password);

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $password);

    public function hasRole(string $role);

    public function addRole(string $role);

    public function removeRole(string $role);
}
