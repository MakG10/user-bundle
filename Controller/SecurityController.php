<?php

namespace MakG\UserBundle\Controller;


use App\Form\User\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
	 * @Route("/sign-in", name="mg_user_security_login")
	 * @Template()
	 */
    public function login()
    {
        $error = null; // TODO
		$form = $this->createForm(LoginForm::class);

		return $this->render('@User/security/auth.html.twig', [
			'form' => $form->createView(),
			'error' => $error,
		]);
	}
}