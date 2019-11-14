<?php

namespace MakG\UserBundle\Event;


use MakG\UserBundle\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public const REGISTRATION_SUCCESS = 'mg_user.registration_success';
    public const REGISTRATION_FAILURE = 'mg_user.registration_failure';
    public const REGISTRATION_COMPLETED = 'mg_user.registration_completed';
    public const REGISTRATION_CONFIRMED = 'mg_user.registration_confirmed';
    public const PASSWORD_RESET_REQUESTED = 'mg_user.password_reset_requested';
    public const PASSWORD_RESET_COMPLETED = 'mg_user.password_reset_completed';

    private $user;
    private $response;

    public function __construct(UserInterface $user)
    {
        $this->setUser($user);
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
