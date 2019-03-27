<?php

namespace MakG\UserBundle\Entity;


interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getId();
    public function getEmail(): ?string;
    public function isEnabled(): bool;
    public function setEnabled(bool $enabled);
    public function getConfirmationToken(): ?string;
    public function setConfirmationToken(?string $token);
    public function hasPasswordRequestExpired(int $ttl): bool;
    public function setPassword(string $password);

    public function getPlainPassword(): ?string;
}
