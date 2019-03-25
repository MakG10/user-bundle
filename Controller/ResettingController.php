<?php

namespace MakG\UserBundle\Controller;


use MakG\UserBundle\Event\UserEvent;
use MakG\UserBundle\Form\ResetPasswordForm;
use MakG\UserBundle\Form\ResettingRequestForm;
use MakG\UserBundle\Manager\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResettingController extends AbstractController
{
    private $userManager;
    private $eventDispatcher;
    private $translator;
    private $requestInterval;
    private $tokenTtl;

    public function __construct(
        UserManagerInterface $userManager,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        int $requestInterval,
        int $tokenTtl
    )
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->requestInterval = $requestInterval;
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * @Route("/request-new-password", name="mg_user_resetting_request")
     */
    public function request(Request $request) {
        $form = $this->createForm(ResettingRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];

            $user = $this->userManager->findUserBy([
                'email' => $email,
                'enabled' => true,
            ]);

            $canRequestAgain = $user && $user->hasPasswordRequestExpired($this->requestInterval); // TODO

            if ($canRequestAgain) {
                $event = new UserEvent($user);
                $this->eventDispatcher->dispatch(UserEvent::PASSWORD_RESET_REQUESTED, $event); // TODO subscriber

                $this->userManager->updateUser($user);

                if (null !== $response = $event->getResponse()) {
                    return $response;
                }
            }

            return $this->redirectToRoute('index'); // TODO
        }

        return $this->render('@User/resetting/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="mg_user_resetting_reset")
     */
    public function reset(string $token, Request $request)
    {
        $user = $this->userManager->findUserBy([
            'token' => $token,
            'enabled' => true,
        ]);

        if (!$user || $user->hasPasswordRequestExpired($this->tokenTtl)) { // TODO
            $this->addFlash('error', $this->translator->trans('Invalid token.'));

            return $this->redirectToRoute('index');
        }


        $form = $this->createForm(ResetPasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);

            $this->userManager->updateUser($user);

            $event = new UserEvent($user);
            $this->eventDispatcher->dispatch(UserEvent::PASSWORD_RESET_COMPLETED, $event); // TODO subscriber

            $this->userManager->updateUser($user);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            // TODO autologin?

            return $this->redirectToRoute('index'); // TODO
        }

        return $this->render('@User/resetting/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}