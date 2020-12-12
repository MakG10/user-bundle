<?php

namespace MakG\UserBundle\Controller;


use MakG\UserBundle\Event\UserEvent;
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
    private $retryTtl;
    private $tokenTtl;
    private $resettingRequestFormType;
    private $resetPasswordFormType;

    public function __construct(
        UserManagerInterface $userManager,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        int $retryTtl,
        int $tokenTtl,
        string $resettingRequestFormType,
        string $resetPasswordFormType
    )
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->retryTtl = $retryTtl;
        $this->tokenTtl = $tokenTtl;
        $this->resettingRequestFormType = $resettingRequestFormType;
        $this->resetPasswordFormType = $resetPasswordFormType;
    }

    /**
     * @Route("/request-new-password", name="mg_user_resetting_request")
     */
    public function request(Request $request) {
        $form = $this->createForm($this->resettingRequestFormType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];

            $user = $this->userManager->findUserBy([
                'email' => $email,
                'enabled' => true,
            ]);

            $canRequestAgain = $user && $user->hasPasswordRequestExpired($this->retryTtl);

            if ($canRequestAgain) {
                $user->setPasswordRequestedAt(new \DateTime());

                $event = new UserEvent($user);
                $this->eventDispatcher->dispatch($event, UserEvent::PASSWORD_RESET_REQUESTED);

                $this->userManager->updateUser($user);

                if (null !== $response = $event->getResponse()) {
                    return $response;
                }
            }

            return $this->redirectToRoute('mg_user_resetting_check_email');
        }

        return $this->render('@User/resetting/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check-email", name="mg_user_resetting_check_email")
     */
    public function checkEmail()
    {
        return $this->render('@User/resetting/check_email.html.twig');
    }

    /**
     * @Route("/reset-password/{token}", name="mg_user_resetting_reset")
     */
    public function reset(string $token, Request $request)
    {
        $user = $this->userManager->findUserBy([
            'confirmationToken' => $token,
            'enabled'           => true,
        ]);

        if (!$user || $user->hasPasswordRequestExpired($this->tokenTtl)) {
            $this->addFlash('error', $this->translator->trans('resetting.error', [], 'MakGUserBundle'));

            return $this->redirectToRoute('mg_user_security_login');
        }


        $form = $this->createForm($this->resetPasswordFormType, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);

            $this->userManager->updateUser($user);

            $event = new UserEvent($user);
            $this->eventDispatcher->dispatch($event, UserEvent::PASSWORD_RESET_COMPLETED);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }

            return $this->redirectToRoute('mg_user_security_login');
        }

        return $this->render('@User/resetting/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
