<?php

namespace MakG\UserBundle\Controller;


use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\Manager\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $userManager;
    private $eventDispatcher;
    private $registrationFormType;

    public function __construct(
        UserManagerInterface $userManager,
        EventDispatcherInterface $eventDispatcher,
        string $registrationFormType
    )
	{
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registrationFormType = $registrationFormType;
    }

	/**
	 * @Route("/sign-up", name="mg_user_registration")
	 */
    public function form(Request $request)
	{
		$user = $this->userManager->createUser();
        $form = $this->createForm($this->registrationFormType, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
		    $event = new UserEvent($user);
            $this->eventDispatcher->dispatch(UserEvent::REGISTRATION_SUCCESS, $event);

			$this->userManager->updateUser($user);

            $this->eventDispatcher->dispatch(UserEvent::REGISTRATION_COMPLETED, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $this->redirectToRoute('index');
		}

		return $this->render('@User/registration/form.html.twig', [
			'form' => $form->createView(),
		]);
	}

    /**
     * @Route("/registration-completed", name="mg_user_registration_activation_required")
     */
	public function activationRequired()
    {
        return $this->render('@User/registration/activation_required.html.twig');
    }

    /**
     * @Route("/confirm-registration/{token}", name="mg_user_registration_confirm")
     */
	public function confirm(string $token)
    {
        $user = $this->userManager->findUserBy(['confirmationToken' => $token]);

        if (null === $user) {
            return $this->render('@User/registration/confirm_error.html.twig');
        }


        $user->setEnabled(true);
        $user->setConfirmationToken(null);

        $this->userManager->updateUser($user);

        return $this->render('@User/registration/confirm_success.html.twig');
    }
}