<?php

namespace MakG\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private string $loginFormType;
    private AuthenticationUtils $authenticationUtils;

    public function __construct(string $loginFormType, AuthenticationUtils $authenticationUtils)
    {
        $this->loginFormType = $loginFormType;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function login()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm($this->loginFormType);

        return
            $this->render('@User/security/login.html.twig', [
                'form' => $form->createView(),
                'error' => $error,
            ]);
    }
}
