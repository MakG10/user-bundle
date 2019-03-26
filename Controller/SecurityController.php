<?php

namespace MakG\UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private $loginFormType;

    public function __construct(string $loginFormType)
    {
        $this->loginFormType = $loginFormType;
    }

    /**
	 * @Route("/sign-in", name="mg_user_security_login")
	 * @Template()
	 */
    public function login()
    {
        $error = null; // TODO
        $form = $this->createForm($this->loginFormType);

		return $this->render('@User/security/auth.html.twig', [
			'form' => $form->createView(),
			'error' => $error,
		]);
	}
}