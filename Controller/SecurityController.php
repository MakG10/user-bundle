<?php

namespace MakG\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $loginFormType;
    private $authenticationUtils;

    public function __construct(string $loginFormType, AuthenticationUtils $authenticationUtils)
    {
        $this->loginFormType       = $loginFormType;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
	 * @Route("/sign-in", name="mg_user_security_login")
	 */
    public function login()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $form  = $this->createForm($this->loginFormType);

        return $this->render(
            '@User/security/login.html.twig',
            [
			'form' => $form->createView(),
			'error' => $error,
		]);
	}
}